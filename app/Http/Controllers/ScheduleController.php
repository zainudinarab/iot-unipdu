<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;




class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['device', 'ruangan'])->get();
        return view('schedules.index', compact('schedules'));
    }

    public function create()
    {
        // $devices = Device::with('ruangans')->get();
        // $ruanganTerhubung = \App\Models\Ruangan::whereHas('device')
        //     ->with(['device' => function ($q) {
        //         $q->withPivot('group_index');
        //     }])->get();
        $ruanganTerhubung = DB::table('device_ruangans')
            ->join('ruangans', 'device_ruangans.ruangan_id', '=', 'ruangans.id')
            ->select('ruangans.id', 'ruangans.name', 'device_ruangans.device_id', 'device_ruangans.group_index')
            ->get();


        // dd($ruanganTerhubung);
        return view('schedules.create', compact('ruanganTerhubung'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'on_time' => 'required|date_format:H:i',
            'off_time' => 'required|date_format:H:i',
            'days' => 'required|array',
            'days.*' => 'integer|min:0|max:6'
        ]);
        // Validasi waktu off harus setelah waktu on
        $on = \Carbon\Carbon::createFromFormat('H:i', $request->on_time);
        $off = \Carbon\Carbon::createFromFormat('H:i', $request->off_time);
        if ($off->lessThanOrEqualTo($on)) {
            return back()->withErrors(['off_time' => 'Waktu OFF harus setelah waktu ON.'])->withInput();
        }
        // Ambil pivot device_ruangans yang sesuai
        $pivot = DB::table('device_ruangans')
            ->where('ruangan_id', $validated['ruangan_id'])
            ->first();
        if (!$pivot) {
            return back()->withErrors(['ruangan_id' => 'Ruangan belum terhubung ke device.']);
        }
        // Tambahkan group_index ke data yang akan disimpan
        $validated['device_id'] = $pivot->device_id;
        $validated['grup_id'] = $pivot->group_index;
        // Simpan jadwal
        $schedule = Schedule::create($validated);
        // Update sys flag
        Device::where('id', $request->device_id)->update(['sys' => true]);
        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);
        $devices = Device::with('ruangans')->get(); // Ambil semua device dengan ruangan terkait
        return view('schedules.edit', compact('schedule', 'devices'));
    }
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'on_time' => 'required|date_format:H:i',
            'off_time' => 'required|date_format:H:i',
            'days' => 'required|array|min:1|max:7',
            'days.*' => 'integer|min:0|max:6',
        ]);
        // Validasi waktu off harus setelah waktu on
        $on = \Carbon\Carbon::createFromFormat('H:i', $request->on_time);
        $off = \Carbon\Carbon::createFromFormat('H:i', $request->off_time);
        if ($off->lessThanOrEqualTo($on)) {
            return back()->withErrors(['off_time' => 'Waktu OFF harus setelah waktu ON.'])->withInput();
        }
        $pivot = DB::table('device_ruangans')
            ->where('ruangan_id', $validated['ruangan_id'])
            ->first();
        if (!$pivot) {
            return back()->withErrors(['ruangan_id' => 'Ruangan belum terhubung ke device.']);
        }
        // Tambahkan group_index ke data yang akan disimpan
        $validated['device_id'] = $pivot->device_id;
        $validated['grup_id'] = $pivot->group_index;
        // dd($validated);
        $schedule->update($validated);
        Device::where('id', $request->device_id)->update(['sys' => true]);
        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
