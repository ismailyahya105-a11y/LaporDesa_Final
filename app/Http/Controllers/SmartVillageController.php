<?php

namespace App\Http\Controllers;

use App\Models\Apbdes;
use App\Models\KontakDesa;
use App\Models\LaporanDarurat;
use App\Models\LokasiDesa;
use App\Models\Lowongan;
use App\Models\Pengumuman;
use App\Models\ProdukUmkm;
use App\Models\User;
use App\Notifications\DaruratBaruNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SmartVillageController extends Controller
{
    public function informasi()
    {
        return view('smart.informasi', ['apbdes' => Apbdes::where('tahun', now()->year)->get(), 'pengumuman' => Pengumuman::where('aktif', true)->latest('tanggal')->get(), 'kontak' => KontakDesa::orderBy('jabatan')->get()]);
    }

    public function darurat(Request $request)
    {
        return view('smart.darurat', ['riwayat' => $request->user()->laporanDarurat()->latest()->limit(5)->get()]);
    }

    public function storeDarurat(Request $request)
    {
        abort_if($request->user()->isAdmin(), 403);
        $data = $request->validate(['latitude' => 'required|numeric|between:-90,90', 'longitude' => 'required|numeric|between:-180,180', 'jenis_darurat' => 'required|string|max:100', 'nomor_telepon' => 'required|string|max:30']);
        $item = $request->user()->laporanDarurat()->create($data)->load('user');
        User::where('role', 'admin')->get()->each->notify(new DaruratBaruNotification($item));

        return back()->with('success', 'Sinyal darurat dikirim. Petugas akan segera menindaklanjuti.');
    }

    public function pasar()
    {
        return view('smart.pasar', ['produk' => ProdukUmkm::latest()->paginate(12), 'lowongan' => Lowongan::whereDate('tanggal', '>=', today())->orderBy('tanggal')->get()]);
    }

    public function photoProduk(ProdukUmkm $produkUmkm): BinaryFileResponse
    {
        abort_if(
            blank($produkUmkm->foto_produk) || ! Storage::disk('public')->exists($produkUmkm->foto_produk),
            404
        );

        return response()->file(Storage::disk('public')->path($produkUmkm->foto_produk), [
            'Cache-Control' => 'public, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function storeProdukUmkm(Request $request)
    {
        abort_if($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'nama_usaha' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'nama_produk' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'kontak' => ['required', 'string', 'max:30'],
            'foto_produk' => ['nullable', 'image', 'max:2048'],
        ]);

        $data['user_id'] = $request->user()->id;
        $data['pemilik'] = $request->user()->name;

        if ($request->hasFile('foto_produk')) {
            $data['foto_produk'] = $request->file('foto_produk')->store('produk-umkm', 'public');
        }

        ProdukUmkm::create($data);

        return redirect()->route('pasar.index')->with('success', 'UMKM atau produk baru berhasil ditambahkan.');
    }

    public function editProdukUmkm(Request $request, ProdukUmkm $produkUmkm)
    {
        $this->ensureProdukOwner($request, $produkUmkm);

        return view('smart.pasar-edit', compact('produkUmkm'));
    }

    public function updateProdukUmkm(Request $request, ProdukUmkm $produkUmkm)
    {
        $this->ensureProdukOwner($request, $produkUmkm);

        $data = $request->validate([
            'nama_usaha' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'nama_produk' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'kontak' => ['required', 'string', 'max:30'],
            'foto_produk' => ['nullable', 'image', 'max:2048'],
            'hapus_foto' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('foto_produk')) {
            if ($produkUmkm->foto_produk) {
                Storage::disk('public')->delete($produkUmkm->foto_produk);
            }
            $data['foto_produk'] = $request->file('foto_produk')->store('produk-umkm', 'public');
        } elseif ($request->boolean('hapus_foto') && $produkUmkm->foto_produk) {
            Storage::disk('public')->delete($produkUmkm->foto_produk);
            $data['foto_produk'] = null;
        }

        unset($data['hapus_foto']);
        $data['pemilik'] = $request->user()->name;
        $produkUmkm->update($data);

        return redirect()->route('pasar.index')->with('success', 'Data UMKM berhasil diperbarui.');
    }

    private function ensureProdukOwner(Request $request, ProdukUmkm $produkUmkm): void
    {
        abort_unless($produkUmkm->user_id === $request->user()->id, 403);
    }

    public function peta()
    {
        return view('smart.peta', ['lokasi' => LokasiDesa::orderBy('kategori')->get()]);
    }

    public function admin()
    {
        return view('smart.admin', ['daruratAktif' => LaporanDarurat::with('user')->where('status', 'aktif')->latest()->get()]);
    }

    public function updateDarurat(Request $request, LaporanDarurat $darurat)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $darurat->update($request->validate(['status' => ['required', Rule::in(['aktif', 'ditangani', 'selesai'])]]));

        return back()->with('success','Status darurat diperbarui.');
    }
}
