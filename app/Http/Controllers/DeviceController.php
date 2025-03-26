<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use PhpMqtt\Client\Facades\MQTT;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;




class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::all();
        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        $ruangans = Ruangan::whereDoesntHave('device')->get();
        return view('devices.create', compact('ruangans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mac_address' => 'required|string|max:255|unique:devices',
            'ruangan_id' => 'required|array|min:1|max:2', // Maksimal 2 ruangan
            'ruangan_id.*' => 'exists:ruangans,id'
        ]);

        $device = Device::create([
            'name' => $request->name,
            'mac_address' => $request->mac_address,
        ]);
        $device->ruangans()->attach($request->ruangan_id);
        return redirect()->route('devices.index')->with('success', 'Device berhasil ditambahkan.');
    }

    public function edit(Device $device)
    {
        $ruangans = Ruangan::whereDoesntHave('device')
            ->orWhereHas('device', function ($query) use ($device) {
                $query->where('devices.id', $device->id);
            })
            ->get();
        return view('devices.edit', compact('device', 'ruangans'));
    }

    public function update(Request $request, Device $device)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mac_address' => 'required|string|max:255|unique:devices,mac_address,' . $device->id,
            'ruangan_id' => 'required|array|min:1|max:2',
            'ruangan_id.*' => 'exists:ruangans,id'
        ]);

        $device->update([
            'name' => $request->name,
            'mac_address' => $request->mac_address,
        ]);

        $device->ruangans()->sync($request->ruangan_id);
        return redirect()->route('devices.index')->with('success', 'Device berhasil diperbarui.');
    }

    public function destroy(Device $device)
    {
        $device->delete();
        return redirect()->route('devices.index')->with('success', 'Device berhasil dihapus.');
    }


    public function syncSchedules($deviceId)
    {
        $device = Device::findOrFail($deviceId);

        // Ambil data dari database langsung agar tidak terkena accessor
        $schedules = DB::table('schedules')
            ->where('device_id', $deviceId)
            ->select(['relay_mask', 'on_time', 'off_time', 'days'])
            ->get();

        $binaryPayloads = [];

        foreach ($schedules as $schedule) {
            $relayMask = pack('C', $schedule->relay_mask); // 1 byte
            $onTime = pack('n', (int) $schedule->on_time); // 2 byte
            $offTime = pack('n', (int) $schedule->off_time); // 2 byte
            $days = pack('C', $schedule->days); // 1 byte

            // Gabungkan ke dalam satu string binary
            $binaryPayloads[] = $relayMask . $onTime . $offTime . $days;
        }

        // Gabungkan semua jadwal dalam satu payload
        $finalPayload = implode('', $binaryPayloads);

        // Kirim ke MQTT dalam format binary
        $this->publishMessage($device->mqtt_topic, $finalPayload);

        // Update status sinkronisasi
        $device->update(['sys' => false]);

        return response()->json([
            'status' => 'success',
            'message' => bin2hex($finalPayload) // Untuk debugging, tampilkan hex string
        ]);
    }

    public function publishMessage($topic, $message)
    {
        $mqtt = MQTT::connection();
        $mqtt->publish($topic, $message);
        $mqtt->disconnect();
        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Message published to topic: ' . $topic,
        //     'message2' => $message
        // ]);
    }
}
