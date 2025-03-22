<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
{
    // Ambil data kelas beserta perangkat terkait
    $kelasList = Kelas::with('perangkat')->get();

    // Perbaiki pengelompokan perangkat relay dan sensor
    $kelasList->transform(function ($kelas) {
        // Kelompokkan perangkat relay
        $kelas->perangkat_relay = $kelas->perangkat->filter(function ($perangkat) {
            return trim($perangkat->tipe) === 'relay'; // Trim untuk menghindari spasi ekstra
        });

        // Kelompokkan perangkat sensor
        $kelas->perangkat_sensor = $kelas->perangkat->filter(function ($perangkat) {
            return trim($perangkat->tipe) === 'sensor'; // Trim untuk menghindari spasi ekstra
        });

        // Hapus perangkat yang tidak dikelompokkan (opsional)
        unset($kelas->perangkat); // Menghapus perangkat yang mencakup semua

        return $kelas;
    });

    return response()->json($kelasList);
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
