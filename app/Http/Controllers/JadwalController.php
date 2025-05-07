<?php

use App\Models\Device;
use App\Models\JadwalKelas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;


class JadwalController extends Controller
{


    public function importFromJson(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|file|mimes:json,txt',
        ]);

        // Ambil isi file dan decode JSON
        $json = file_get_contents($request->file('file'));
        $data = json_decode($json, true);

        if (!$data) {
            return response()->json(['error' => 'Format JSON tidak valid'], 400);
        }

        // Hapus semua data lama
        JadwalKelas::truncate();

        // Simpan data baru
        foreach ($data as $item) {
            JadwalKelas::create([
                'ruangan' => $item['ruangan'],
                'jam_on' => $item['jam_on'],
                'jam_off' => $item['jam_off'],
                'days' => $item['days'], // ini 1 hari per baris
            ]);
        }

        return response()->json(['message' => 'Jadwal berhasil diimpor.'], 200);
    }
    public function exportJadwal(Device $device)
    {
        // Ambil semua ruangan yang dikontrol device ini
        $ruanganGroupMap = $device->ruangans->pluck('pivot.indek_group', 'nama');

        // Ambil semua jadwal hanya untuk ruangan terkait
        $jadwals = JadwalKelas::whereIn('ruangan', $ruanganGroupMap->keys())->get();

        // Gabungkan berdasarkan ruangan + jam_on + jam_off
        $grouped = $jadwals->groupBy(function ($item) {
            return $item->ruangan . '|' . $item->jam_on . '|' . $item->jam_off;
        });

        $hasil = $grouped->map(function ($group) use ($ruanganGroupMap) {
            $first = $group->first();
            $combinedDays = $group->flatMap(fn($item) => json_decode($item->days, true))->unique()->values()->all();

            return [
                'ruangan' => $first->ruangan,
                'jam_on' => $this->timeToMinutes($first->jam_on),
                'jam_off' => $this->timeToMinutes($first->jam_off),
                'days' => $this->daysToBinaryInt($combinedDays),
                'indek_group' => $ruanganGroupMap[$first->ruangan] ?? null,
            ];
        })->values();

        return response()->json($hasil);
    }

    private function timeToMinutes($time)
    {
        [$hour, $minute] = explode(':', $time);
        return ((int)$hour * 60) + (int)$minute;
    }

    private function daysToBinaryInt(array $days)
    {
        $binary = 0;
        foreach ($days as $day) {
            $binary |= (1 << $day);
        }
        return $binary;
    }
}
