<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lantai;
use Illuminate\Http\Request;

class LantaiController extends Controller
{
    // Mendapatkan semua data lantai
    public function index()
    {
        $lantais = Lantai::with('gedung')->get();
        return response()->json($lantais);
    }

    // Menyimpan data lantai baru
    public function store(Request $request)
    {
        $request->validate([
            'gedung_id' => 'required|exists:gedungs,id',
            'nomor' => 'required|integer',
        ]);

        $lantai = Lantai::create($request->all());
        return response()->json($lantai, 201);
    }

    // Mendapatkan data lantai berdasarkan ID
    public function show($id)
    {
        $lantai = Lantai::with('gedung')->findOrFail($id);
        return response()->json($lantai);
    }

    // Mengupdate data lantai
    public function update(Request $request, $id)
    {
        $request->validate([
            'gedung_id' => 'required|exists:gedungs,id',
            'nomor' => 'required|integer',
        ]);

        $lantai = Lantai::findOrFail($id);
        $lantai->update($request->all());
        return response()->json($lantai);
    }

    // Menghapus data lantai
    public function destroy($id)
    {
        $lantai = Lantai::findOrFail($id);
        $lantai->delete();
        return response()->json(null, 204);
    }
}