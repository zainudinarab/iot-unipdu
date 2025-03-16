<?php

namespace App\Http\Controllers;

use App\Models\StatusPerangkat;
use Illuminate\Http\Request;

class StatusPerangkatController extends Controller
{
    // Menampilkan daftar status perangkat
    public function index()
    {
        $statusPerangkat = StatusPerangkat::with('perangkat')->get();
        return response()->json($statusPerangkat);
    }

    // Menambahkan status perangkat baru
    public function store(Request $request)
    {
        $request->validate([
            'perangkat_id' => 'required|exists:perangkat,id',
            'status' => 'required|string|max:255',
        ]);

        $status = StatusPerangkat::create([
            'perangkat_id' => $request->perangkat_id,
            'status' => $request->status,
        ]);

        return response()->json($status, 201);
    }

    // Mengupdate status perangkat
    public function update(Request $request, $id)
    {
        $request->validate([
            'perangkat_id' => 'required|exists:perangkat,id',
            'status' => 'required|string|max:255',
        ]);

        $status = StatusPerangkat::findOrFail($id);
        $status->update([
            'perangkat_id' => $request->perangkat_id,
            'status' => $request->status,
        ]);

        return response()->json($status);
    }

    // Menghapus status perangkat
    public function destroy($id)
    {
        $status = StatusPerangkat::findOrFail($id);
        $status->delete();

        return response()->json(['message' => 'Status perangkat deleted successfully']);
    }
}
