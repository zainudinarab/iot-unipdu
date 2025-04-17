<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Device;
use Illuminate\Http\Request;


class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['device', 'ruangan'])->get();
        return view('schedules.index', compact('schedules'));
    }

    public function create()
    {
        $devices = Device::with('ruangans')->get();
        return view('schedules.create', compact('devices'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'device_id' => 'required|exists:devices,id',
            'ruangan_id' => 'required|exists:ruangans,id',
            'relay_mask' => 'required|integer|min:1',
            'on_time' => 'required|date_format:H:i',
            'off_time' => 'required|date_format:H:i|after:on_time',
            'days' => 'required|array',
            'days.*' => 'integer|min:0|max:6'
        ]);
        // Ambil group_index dari relasi pivot
        $device = Device::with('ruangans')->findOrFail($validated['device_id']);
        $pivot = $device->ruangans->find($validated['ruangan_id']);
        if (!$pivot) {
            return back()->withErrors('Ruangan tidak ditemukan dalam device tersebut.');
        }
        // Tambahkan group_index ke data yang akan disimpan
        $validated['grup_id'] = $pivot->pivot->group_index;
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
            'device_id' => 'required|exists:devices,id',
            'ruangan_id' => 'required|exists:ruangans,id',
            'relay_mask' => 'required|integer|min:0|max:1023',
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
        $device = Device::with('ruangans')->findOrFail($validated['device_id']);
        $pivot = $device->ruangans->find($validated['ruangan_id']);

        if (!$pivot || !$pivot->pivot || !isset($pivot->pivot->group_index)) {
            return back()->withErrors(['ruangan_id' => 'Group index tidak ditemukan untuk device dan ruangan ini.'])->withInput();
        }
        $validated['grup_id'] = $pivot->pivot->group_index;
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
