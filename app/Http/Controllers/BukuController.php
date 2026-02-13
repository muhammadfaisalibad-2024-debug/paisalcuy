<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index()
    {
        $buku = Buku::with('kategori')->orderBy('idbuku', 'desc')->get();
        return view('buku.index', compact('buku'));
    }

    public function create()
    {
        $kategori = Kategori::all();
        return view('buku.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|max:20',
            'judul' => 'required|max:500',
            'pengarang' => 'required|max:200',
            'idkategori' => 'required|exists:kategori,idkategori'
        ]);

        Buku::create($request->all());

        return redirect()->route('buku.index')
            ->with('success', 'Buku berhasil ditambahkan');
    }

    public function show(Buku $buku)
    {
        return view('buku.show', compact('buku'));
    }

    public function edit(Buku $buku)
    {
        $kategori = Kategori::all();
        return view('buku.edit', compact('buku', 'kategori'));
    }

    public function update(Request $request, Buku $buku)
    {
        $request->validate([
            'kode' => 'required|max:20',
            'judul' => 'required|max:500',
            'pengarang' => 'required|max:200',
            'idkategori' => 'required|exists:kategori,idkategori'
        ]);

        $buku->update($request->all());

        return redirect()->route('buku.index')
            ->with('success', 'Buku berhasil diupdate');
    }

    public function destroy(Buku $buku)
    {
        $buku->delete();

        return redirect()->route('buku.index')
            ->with('success', 'Buku berhasil dihapus');
    }
}
