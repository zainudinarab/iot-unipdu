<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        return response()->json(Kelas::with('lantai.gedung')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'lantai_id' => 'required|exists:lantais,id',
            'nomor' => 'required|string',
        ]);

        $kelas = Kelas::create($request->only(['lantai_id', 'nomor']));
        return response()->json($kelas, 201);
    }

    public function show($id)
    {
        return response()->json(Kelas::with('lantai.gedung')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->only(['lantai_id', 'nomor']));
        return response()->json($kelas);
    }

    public function destroy($id)
    {
        Kelas::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}
