<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Polling;
use App\Models\SuratPengajuan;
use App\Models\User;
use App\Models\Usulan;
use App\Notifications\LaporanBaruNotification;
use App\Notifications\StatusLaporanNotification;
use App\Notifications\TanggapanBaruNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LaporDesaFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_dashboard_and_community_cannot(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $community = User::factory()->create(['role' => 'masyarakat']);

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk()->assertSee('Dashboard Admin');
        $this->actingAs($community)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_new_report_notifies_admin_and_status_notifies_owner(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'masyarakat']);
        $category = Kategori::create(['nama' => 'Jalan']);

        $this->actingAs($owner)->post(route('laporan.store'), [
            'judul' => 'Jalan rusak', 'kategori_id' => $category->id, 'isi_laporan' => 'Berlubang besar.',
        ])->assertRedirect();
        Notification::assertSentTo($admin, LaporanBaruNotification::class);

        $laporan = Laporan::firstOrFail();
        $this->actingAs($admin)->patch(route('laporan.update', $laporan), ['status' => 'diproses'])->assertRedirect();
        Notification::assertSentTo($owner, StatusLaporanNotification::class);
        $this->actingAs($admin)->post(route('tanggapan.store'), ['laporan_id' => $laporan->id, 'isi_tanggapan' => 'Petugas sedang meninjau lokasi.'])->assertRedirect();
        Notification::assertSentTo($owner, TanggapanBaruNotification::class);
    }

    public function test_android_api_login_and_report_access_are_scoped(): void
    {
        $user = User::factory()->create(['role' => 'masyarakat', 'password' => 'password']);
        $other = User::factory()->create(['role' => 'masyarakat']);
        $category = Kategori::create(['nama' => 'Sampah']);
        $otherReport = Laporan::create(['user_id' => $other->id, 'kategori_id' => $category->id, 'judul' => 'Lain', 'isi_laporan' => 'Data', 'status' => 'menunggu']);

        $token = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()->assertJsonStructure(['token', 'user'])->json('token');

        $this->withToken($token)->postJson('/api/laporan', [
            'judul' => 'Sampah menumpuk', 'kategori_id' => $category->id, 'isi_laporan' => 'Mohon diangkut.',
        ])->assertCreated()->assertJsonPath('data.user_id', $user->id);
        $this->withToken($token)->getJson('/api/laporan/'.$otherReport->id)->assertForbidden();
    }

    public function test_complete_community_report_flow_with_photo_and_views(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'masyarakat']);
        $other = User::factory()->create(['role' => 'masyarakat']);
        $category = Kategori::create(['nama' => 'Infrastruktur']);
        Laporan::create([
            'user_id' => $other->id,
            'kategori_id' => $category->id,
            'judul' => 'Laporan pengguna lain',
            'isi_laporan' => 'Tidak boleh terlihat.',
            'status' => 'menunggu',
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()->assertSee('Belum ada laporan');
        $this->actingAs($user)->get(route('laporan.create'))
            ->assertOk()->assertSee('Infrastruktur');
        $this->actingAs($user)->post(route('laporan.store'), [])
            ->assertSessionHasErrors(['judul', 'kategori_id', 'isi_laporan']);

        $response = $this->actingAs($user)->post(route('laporan.store'), [
            'judul' => 'Jembatan rusak',
            'kategori_id' => $category->id,
            'isi_laporan' => 'Papan jembatan patah dan membahayakan warga.',
            'foto' => UploadedFile::fake()->image('jembatan.jpg'),
        ]);
        $laporan = Laporan::whereBelongsTo($user)->firstOrFail();
        $response->assertRedirect(route('laporan.show', $laporan));
        Storage::disk('public')->assertExists($laporan->foto);

        $this->actingAs($user)->get(route('laporan.index'))
            ->assertOk()->assertSee('Jembatan rusak')->assertDontSee('Laporan pengguna lain');
        $this->actingAs($user)->get(route('laporan.show', $laporan))
            ->assertOk()->assertSee('Jembatan rusak')->assertSee('Infrastruktur')
            ->assertSee('Belum ada tanggapan')->assertSee('Perjalanan Laporan')
            ->assertSee(route('laporan.photo', $laporan), false);

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('laporan.show', $laporan))
            ->assertOk()->assertSee('Jembatan rusak')
            ->assertSee(route('laporan.photo', $laporan), false);
        $this->actingAs($admin)->get(route('laporan.photo', $laporan))->assertOk();
        $this->actingAs($other)->get(route('laporan.photo', $laporan))->assertForbidden();

        $this->actingAs($user)->get(route('profile.edit'))->assertOk();
        $this->post(route('logout'))->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_report_form_creates_default_categories_when_production_database_is_empty(): void
    {
        $user = User::factory()->create(['role' => 'masyarakat']);

        $this->actingAs($user)->get(route('laporan.create'))
            ->assertOk()
            ->assertSee('Infrastruktur')
            ->assertSee('Lingkungan');

        $this->assertDatabaseCount('kategoris', 4);
    }

    public function test_smart_village_community_services_and_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'masyarakat']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)->post(route('surat.store'), [
            'jenis_surat' => 'sku', 'keperluan' => 'Pengajuan izin usaha', 'nomor_telepon' => '08123456789',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('surat_pengajuan', ['user_id' => $user->id, 'status' => 'diajukan']);

        $this->actingAs($user)->post(route('darurat.store'), [
            'latitude' => -6.2, 'longitude' => 106.8, 'jenis_darurat' => 'Medis', 'nomor_telepon' => '08123456789',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('laporan_darurat', ['user_id' => $user->id, 'status' => 'aktif']);

        $this->actingAs($user)->post(route('usulan.store'), ['judul' => 'Taman Desa', 'isi' => 'Membangun taman bermain.']);
        $usulan = Usulan::firstOrFail();
        $this->post(route('usulan.vote', $usulan))->assertRedirect();
        $this->assertDatabaseHas('usulan_vote', ['usulan_id' => $usulan->id, 'user_id' => $user->id]);

        $polling = Polling::create(['judul' => 'Hari kerja bakti', 'aktif' => true]);
        $option = $polling->options()->create(['opsi' => 'Minggu']);
        $this->post(route('polling.vote', $polling), ['polling_option_id' => $option->id])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('polling_vote', ['polling_id' => $polling->id, 'user_id' => $user->id]);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Darurat Aktif');
        $this->get(route('kategori.index'))->assertOk()->assertSee('Kategori Laporan');
        $this->get(route('admin.smart'))->assertOk()->assertSee('Medis');
        $surat = SuratPengajuan::firstOrFail();
        $this->patch(route('admin.surat.update', $surat), ['status' => 'diproses', 'catatan' => 'Sedang diverifikasi.'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('surat_pengajuan', ['id' => $surat->id, 'status' => 'diproses']);

        foreach (['surat.index', 'informasi.index', 'pasar.index', 'usulan.index', 'polling.index', 'peta.index'] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk();
        }
        $this->get(route('darurat.index'))->assertOk();
        $this->get(route('surat.index'))->assertSee('diproses')->assertSee('Sedang diverifikasi.');
        $notification = $user->notifications()->where('data->type', 'status_surat')->firstOrFail();
        $this->get(route('notifications.read', $notification))->assertRedirect(route('surat.index'));
        $this->actingAs($user)->get(route('kategori.index'))->assertForbidden();
    }

    public function test_community_cannot_vote_on_inactive_or_expired_polling(): void
    {
        $user = User::factory()->create(['role' => 'masyarakat']);
        $inactive = Polling::create(['judul' => 'Polling ditutup', 'aktif' => false]);
        $expired = Polling::create([
            'judul' => 'Polling berakhir',
            'aktif' => true,
            'berakhir_pada' => now()->subMinute(),
        ]);

        $inactiveOption = $inactive->options()->create(['opsi' => 'Pilihan A']);
        $expiredOption = $expired->options()->create(['opsi' => 'Pilihan B']);

        $this->actingAs($user)
            ->post(route('polling.vote', $inactive), ['polling_option_id' => $inactiveOption->id])
            ->assertUnprocessable();
        $this->post(route('polling.vote', $expired), ['polling_option_id' => $expiredOption->id])
            ->assertUnprocessable();

        $this->assertDatabaseMissing('polling_vote', ['user_id' => $user->id]);
    }
}
