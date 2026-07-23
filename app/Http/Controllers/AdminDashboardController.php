<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\LaporanDarurat;
use App\Models\SuratPengajuan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $statusCounts = Laporan::query()->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status');
        $categories = Kategori::query()->withCount('laporan')->orderByDesc('laporan_count')->get();
        $months = Laporan::query()->whereYear('created_at', now()->year)
            ->get(['created_at'])->countBy(fn (Laporan $laporan) => $laporan->created_at->month);

        return view('admin.dashboard', [
            'total' => Laporan::count(),
            'jumlahWarga' => User::where('role', 'masyarakat')->count(),
            'suratMasuk' => SuratPengajuan::where('status', 'diajukan')->count(),
            'daruratAktif' => LaporanDarurat::where('status', 'aktif')->count(),
            'menunggu' => (int) ($statusCounts['menunggu'] ?? 0),
            'diproses' => (int) ($statusCounts['diproses'] ?? 0),
            'selesai' => (int) ($statusCounts['selesai'] ?? 0),
            'latestReports' => Laporan::with(['user', 'kategori'])->latest()->limit(10)->get(),
            'categoryLabels' => $categories->pluck('nama'),
            'categoryValues' => $categories->pluck('laporan_count'),
            'monthLabels' => collect(range(1, 12))->map(fn ($month) => now()->month($month)->translatedFormat('M')),
            'monthValues' => collect(range(1, 12))->map(fn ($month) => (int) ($months[$month] ?? 0)),
        ]);
    }
}
