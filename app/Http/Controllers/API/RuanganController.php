<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\Ruangan;
class RuanganController extends Controller
{
    /**
     * Menampilkan daftar semua ruangan
     */
    public function index()
    {
        return Ruangan::with(['gedung', 'lantai'])->get();
    }

    /**
     * Menyimpan ruangan baru
     */
    public function store(Request $request)
{
    try {
        $messages = [
            'name.required' => 'Nama ruangan wajib diisi.',
            'name.unique' => 'Nama ruangan sudah digunakan pada gedung dan lantai yang dipilih.',
            'gedung_id.required' => 'Gedung harus dipilih.',
            'gedung_id.exists' => 'Gedung yang dipilih tidak valid.',
            'lantai_id.required' => 'Lantai harus dipilih.',
            'lantai_id.exists' => 'Lantai yang dipilih tidak valid.',
        ];

        $validated = $request->validate([
            'gedung_id' => 'required|exists:gedungs,id',
            'lantai_id' => 'required|exists:lantais,id',
            'name' => [
                'required',
                Rule::unique('ruangans')->where(fn ($query) => 
                    $query->where('gedung_id', $request->gedung_id)
                          ->where('lantai_id', $request->lantai_id)
                ),
            ],
        ], $messages); // Tambahkan custom message

        // Simpan data ruangan
        $ruangan = Ruangan::create($validated);

        return response()->json([
            'message' => 'Ruangan berhasil ditambahkan.',
            'data' => $ruangan
        ], 201);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validasi gagal!',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan pada server.',
            'error' => $e->getMessage()
        ], 500);
    }
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
