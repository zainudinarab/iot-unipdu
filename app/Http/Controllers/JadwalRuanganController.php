<?php

namespace App\Http\Controllers;

use App\Models\JadwalRuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class JadwalRuanganController extends Controller
{
    public function index()
    {
        // $jadwals = JadwalRuangan::all();
        $jadwals = JadwalRuangan::paginate(10); // 10 data per halaman

        return view('jadwal_ruangans.index', compact('jadwals'));
    }

    public function create()
    {
        // return view('jadwal_ruangans.form');
        return view('jadwal_ruangans.form', ['jadwal' => new JadwalRuangan()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ruangan' => 'required|string',
            'on_time' => 'required|date_format:H:i',
            'off_time' => 'required|date_format:H:i',
            'days'    => 'required|integer|min:0|max:6',
        ]);

        JadwalRuangan::create($validated);
        return redirect()->route('jadwal-ruangans.index')->with('success', 'Data berhasil ditambahkan!');
    }

    public function show($id)
    {
        $jadwal = JadwalRuangan::findOrFail($id);
        return view('jadwal_ruangans.show', compact('jadwal'));
    }

    public function edit($id)
    {
        $jadwal = JadwalRuangan::findOrFail($id);
        return view('jadwal_ruangans.form', compact('jadwal'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalRuangan::findOrFail($id);

        $validated = $request->validate([
            'ruangan' => 'required|string',
            'on_time' => 'required|date_format:H:i',
            'off_time' => 'required|date_format:H:i',
            'days'    => 'required|integer|min:0|max:6',
        ]);

        $jadwal->update($validated);
        return redirect()->route('jadwal-ruangans.index')->with('success', 'Data berhasil diubah!');
    }

    public function destroy($id)
    {
        $jadwal = JadwalRuangan::findOrFail($id);
        $jadwal->delete();
        return redirect()->route('jadwal-ruangans.index')->with('success', 'Data berhasil dihapus!');
    }

    public function importFromJsonUrl()
    {
        $url = 'https://supersisfo.siakad.unipdu.ac.id/wifikode/remoteruang.txt';

        try {
            // Ambil data dari URL
            $response = Http::get($url);

            if (!$response->ok()) {
                return response()->json(['error' => 'Gagal mengunduh data JSON'], 500);
            }

            $data = $response->json();

            if (!is_array($data)) {
                return response()->json(['error' => 'Format JSON tidak valid'], 422);
            }

            // Hapus semua jadwal lama
            JadwalRuangan::truncate();

            // Simpan data baru
            foreach ($data as $item) {
                JadwalRuangan::create([
                    'ruangan' => $item['ruangan'] ?? '-',
                    'on_time' => $item['jam_on'],
                    'off_time' => $item['jam_off'],
                    'days' => $item['days'], // 0–6
                ]);
            }

            return redirect()->route('jadwal-ruangans.index')->with('success', 'Data berhasil ditambahkan!');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
