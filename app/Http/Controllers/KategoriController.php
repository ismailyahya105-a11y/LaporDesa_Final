<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        return view('smart.kategori', ['items' => Kategori::withCount('laporan')->orderBy('nama')->get()]);
    }

    public function store(Request $request)
    {
        Kategori::create($request->validate(['nama' => 'required|string|max:100|unique:kategoris,nama', 'deskripsi' => 'nullable|string|max:1000']));

        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function update(Request $request, Kategori $kategori)
    {
        $kategori->update($request->validate(['nama' => 'required|string|max:100|unique:kategoris,nama,'.$kategori->id, 'deskripsi' => 'nullable|string|max:1000']));

        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        abort_if($kategori->laporan()->exists(), 422, 'Kategori masih digunakan laporan.');
        $kategori->delete();

        return back()->with('success', 'Kategori dihapus.');
    }
}
