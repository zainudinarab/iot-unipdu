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
            'grup_id' => 'required',
            'on_time' => 'required|date_format:H:i',
            'off_time' => 'required|date_format:H:i|after:on_time',
            'days' => 'required|array',
            'days.*' => 'integer|min:0|max:6'
        ]);

        $schedule = Schedule::create($validated);
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
        $request->validate([
            'device_id' => 'required|exists:devices,id',
            'ruangan_id' => 'required|exists:ruangans,id',
            'relay_mask' => 'required|integer|min:0|max:1023',
            'grup_id' => 'required',
            'on_time' => 'required|date_format:H:i',  // Format harus "07:30"
            'off_time' => 'required|date_format:H:i',
            'days' => 'required|array|min:1|max:7', // Hari harus diisi dan maksimal 7
            'days.*' => 'integer|min:0|max:6', // Setiap hari harus valid (0=Minggu, 6=Sabtu)
        ]);

        $schedule->update($request->all());
        Device::where('id', $request->device_id)->update(['sys' => true]);
        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
