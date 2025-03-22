<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Gedung;
use App\Models\Lantai;
use Illuminate\Http\Request;

class GedungController extends Controller
{
    // Mendapatkan semua data gedung
    public function index()
    {
        $gedungs = Gedung::all();
        return response()->json($gedungs);
    }

    // Menyimpan data gedung baru
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|unique:gedungs',
                'keterangan' => 'nullable|string',
                'jumlah_lantai' => 'required|integer|min:1', // Validasi jumlah lantai
            ]);

            // Membuat Gedung baru
            $gedung = Gedung::create([
                'nama' => $request->nama,
                'keterangan' => $request->keterangan,
                'jumlah_lantai' => $request->jumlah_lantai,
            ]);

            // Membuat Lantai sesuai dengan jumlah lantai yang dimasukkan
            for ($i = 1; $i <= $request->jumlah_lantai; $i++) {
                Lantai::create([
                    'gedung_id' => $gedung->id,
                    'nomor' => $i, // Nomor lantai (misalnya: 1, 2, 3, dst)
                ]);
            }

            return response()->json($gedung, 201);
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

    // Mendapatkan data gedung berdasarkan ID
    public function show($id)
    {
        $gedung = Gedung::findOrFail($id);
        return response()->json($gedung);
    }

    // Mengupdate data gedung
    public function update(Request $request, $id)
    {
        $gedung = Gedung::find($id);
        if (!$gedung) {
            return response()->json(['message' => 'Gedung tidak ditemukan'], 404);
        }
        try {
            $request->validate([
                'nama' => 'required|string|unique:gedungs,nama,' . $gedung->id,
                'keterangan' => 'nullable|string',
                'jumlah_lantai' => 'required|integer|min:1', // Validasi jumlah lantai
            ]);
            // Jika jumlah lantai berubah, kita perlu cek kelas yang terkait dengan lantai yang akan dihapus
            if ($gedung->jumlah_lantai !== $request->jumlah_lantai) {

                // Ambil lantai yang ada beserta kelasnya
                $existingLantai = $gedung->lantais()->with('kelas')->get();
                // Lantai yang akan dihapus, yaitu lantai yang nomor-nya lebih besar dari jumlah lantai yang baru
                $lantaiToDelete = $existingLantai->where('nomor', '>', $request->jumlah_lantai);
                // Cek apakah lantai yang akan dihapus memiliki kelas yang terkait
                foreach ($lantaiToDelete as $lantai) {
                    if ($lantai->kelas->count() > 0) {
                        // Jika ada kelas yang terkait, kita tidak bisa mengupdate jumlah lantai
                        return response()->json(['message' => 'Tidak bisa mengupdate jumlah lantai karena ada kelas yang terkait dengan lantai yang akan dihapus'], 400);
                    }
                }
            }
            // Update gedung tanpa mengubah lantai jika jumlah lantai tidak berubah
            $jumlahlantai = $gedung->jumlah_lantai;
            $gedung->update([
                'nama' => $request->nama,
                'keterangan' => $request->keterangan,
                'jumlah_lantai' => $request->jumlah_lantai,
            ]);
            // Jika jumlah lantai berubah, lakukan update lantai
            if ($jumlahlantai !== $request->jumlah_lantai) {
                // return response()->json(['message' => 'Tidak bisa mengupdat dihapus'], 400);
                // Hapus lantai yang tidak terpakai
                $existingLantai->where('nomor', '>', $request->jumlah_lantai)->each(function ($lantai) {
                    $lantai->delete(); // Hapus lantai
                });
                // Menambahkan lantai baru jika jumlah lantai bertambah
                for ($i = 1; $i <= $request->jumlah_lantai; $i++) {
                    if (!$existingLantai->contains('nomor', $i)) {
                        // Jika lantai dengan nomor tertentu belum ada, buat lantai baru
                        Lantai::create([
                            'gedung_id' => $gedung->id,
                            'nomor' => $i,
                        ]);
                    }
                }
            }

            return response()->json($gedung);
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


    // Menghapus data gedung
    public function destroy($id)
    {
        // Cari gedung berdasarkan ID
        $gedung = Gedung::find($id);
        if (!$gedung) {
            return response()->json(['message' => 'Gedung tidak ditemukan'], 404);
        }
        // Periksa apakah ada kelas yang terkait dengan lantai-lantai di gedung ini
        $lantais = $gedung->lantais()->with('kelas')->get();
        // Cek apakah ada lantai yang memiliki kelas
        foreach ($lantais as $lantai) {
            if ($lantai->kelas->count() > 0) {
                return response()->json(['message' => 'Tidak bisa menghapus gedung karena ada kelas yang terkait dengan lantai'], 400);
            }
        }
        // Jika tidak ada kelas yang terkait, hapus gedung beserta lantai dan kelas terkait
        $gedung->delete();
        return response()->json(['message' => 'Gedung berhasil dihapus']);
    }
}
