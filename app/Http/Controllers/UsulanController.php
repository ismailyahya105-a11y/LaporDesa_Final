<?php

namespace App\Http\Controllers;

use App\Models\Usulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsulanController extends Controller
{
    public function index()
    {
        return view('smart.usulan', ['items' => Usulan::with('user')->latest()->paginate(12)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['judul' => 'required|string|max:255', 'isi' => 'required|string|max:5000']);
        $request->user()->usulan()->create($data);

        return back()->with('success', 'Usulan berhasil dipublikasikan.');
    }

    public function vote(Request $request, Usulan $usulan)
    {
        DB::transaction(function () use ($request, $usulan) {
            $attached = $usulan->voters()->whereKey($request->user()->id)->exists();
            if ($attached) {
                $usulan->voters()->detach($request->user()->id);
                $usulan->decrement('jumlah_vote');
            } else {
                $usulan->voters()->attach($request->user()->id);
                $usulan->increment('jumlah_vote');
            }
        });

        return back();
    }
}
