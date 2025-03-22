<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    /**
     * Menampilkan daftar semua ruangan
     */
    public function index()
    {
        $ruangans = Ruangan::with(['lantai', 'gedung'])->get();
        // dd($ruangans->perangkat);
        // Perbaiki pengelompokan perangkat relay dan sensor
        $ruangans->transform(function ($ruangans) {
            // Kelompokkan perangkat relay
            $ruangans->perangkat_relay = $ruangans->perangkats->filter(function ($perangkat) {
                return trim($perangkat->tipe) === 'relay'; // Trim untuk menghindari spasi ekstra
            });
            // Kelompokkan perangkat sensor
            $ruangans->perangkat_sensor = $ruangans->perangkats->filter(function ($perangkat) {
                return trim($perangkat->tipe) === 'sensor'; // Trim untuk menghindari spasi ekstra
            });
            // Hapus perangkat yang tidak dikelompokkan (opsional)
            unset($ruangans->perangkat); // Menghapus perangkat yang mencakup semua
            return $ruangans;
        });


        return response()->json($ruangans);
    }

    /**
     * Menyimpan ruangan baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'lantai_id' => 'required|exists:lantais,id',
            'gedung_id' => 'required|exists:gedungs,id',
            'name' => 'required|string|max:255|unique:ruangans, name,NULL,id,gedung_id,' . $request->gedung_id . ',lantai_id,' . $request->lantai_id,
        ]);

        // Menyimpan data ruangan
        $ruangan = Ruangan::create($validated);

        return response()->json($ruangan, 201);
    }

    /**
     * Menampilkan detail ruangan tertentu
     */
    public function show($id)
    {
        $ruangan = Ruangan::with(['lantai', 'gedung'])->findOrFail($id);
        return response()->json($ruangan);
    }

    /**
     * Memperbarui data ruangan
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'lantai_id' => 'required|exists:lantais,id',
            'gedung_id' => 'required|exists:gedungs,id',
            'name' => 'required|string|max:255|unique:ruangans,name,' . $id . ',id,gedung_id,' . $request->gedung_id . ',lantai_id,' . $request->lantai_id,
        ]);

        // Mencari dan memperbarui ruangan
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->update($validated);

        return response()->json($ruangan);
    }

    /**
     * Menghapus ruangan
     */
    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return response()->json(null, 204);
    }
}
