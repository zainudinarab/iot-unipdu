<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Perangkat;
use Illuminate\Http\Request;

class PerangkatController extends Controller
{
    public function index()
    {
        return response()->json(Perangkat::with('kelas.lantai.gedung')->get());
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'kelas_id' => 'required|exists:kelas,id',
                'nama' => 'required|string',
                'kategori' => 'required|string',
                'tipe' => 'required|string',
            ]);
            $lastNumber = Perangkat::where('kelas_id', $validated['kelas_id'])
                ->where('kategori', $validated['kategori'])
                ->max('nomor_urut');
            $nomorUrut = $lastNumber ? $lastNumber + 1 : 1;
            $perangkat = Perangkat::create([
                'kelas_id' => $validated['kelas_id'],
                'tipe' => $validated['tipe'],
                'nama' => $validated['nama'],
                'kategori' => $validated['kategori'],
                'nomor_urut' => $nomorUrut,
                'topic_mqtt' => '', // Mengosongkan nilai ini karena kita akan mengisi setelah
            ]);
            // Mengupdate kolom mqtt_topic menggunakan accessor
            $perangkat->topic_mqtt = $perangkat->mqttTopic; // Memanggil accessor untuk mendapatkan mqtt_topic yang dihasilkan
            $perangkat->save();

            return response()->json([
                'success' => true,
                'message' => 'Perangkat berhasil ditambahkan.',
                'data' => $perangkat
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        return response()->json(Perangkat::with('kelas.lantai.gedung')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $perangkat = Perangkat::findOrFail($id);
        $perangkat->update($request->only(['kelas_id', 'tipe', 'nama', 'topic_mqtt']));
        return response()->json($perangkat);
    }

    public function destroy($id)
    {
        Perangkat::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}
