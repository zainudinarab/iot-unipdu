<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;

class RuanganController extends Controller
{

    public function index()
    {
        $ruangans = Ruangan::with('perangkat.status')->get();
        // dd($ruangans);
        return view('ruangan.index', compact('ruangans'));
    }
    // create
    public function create()
    {
        return view('ruangan.create');
    }

    // Menambahkan ruangan baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $ruangan = Ruangan::create([
            'nama' => $request->nama,
        ]);

        // view
        return redirect()->route('ruangan.index')->with('success', 'Ruangan created successfully');
    }

    // Menampilkan detail ruangan berdasarkan ID
    public function show($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        return view('ruangan.show', compact('ruangan'));
    }

    // Mengupdate data ruangan
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $ruangan = Ruangan::findOrFail($id);
        $ruangan->update([
            'nama' => $request->nama,
        ]);

        return response()->json($ruangan);
    }

    // Menghapus ruangan
    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return response()->json(['message' => 'Ruangan deleted successfully']);
    }
}
