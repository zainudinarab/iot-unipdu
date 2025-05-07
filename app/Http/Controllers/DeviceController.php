<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use PhpMqtt\Client\Facades\MQTT;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Str;
use App\Models\Perangkat;
use Nette\Utils\Random;
use App\Models\JadwalRuangan;

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
            'ruangan_id' => 'required|array|min:1|max:3', // Maksimal 3 ruangan
            'ruangan_id.*' => 'exists:ruangans,id',
            'device_type' => 'nullable|string|max:255',
            'device_model' => 'nullable|string|max:255',
            'firmware_version' => 'nullable|string|max:255',
            'ip_address' => 'nullable|ip',
            'location' => 'nullable|string|max:255',
        ]);

        // Buat Device baru
        $device = Device::create([
            'name' => $request->name,
            'mac_address' => $request->mac_address,
            'mqtt_topic' => 'esp32-' . Str::slug($request->name),
            'device_type' => $request->device_type,
            'device_model' => $request->device_model,
            'firmware_version' => $request->firmware_version,
            'ip_address' => $request->ip_address,
            'location' => $request->location,
            'status' => 'offline', // default status awal
        ]);

        // Step 2: Generate MQTT Topic Unik
        // Contoh format: esp32-s3-12
        $deviceId = str_pad($device->id, 5, '0', STR_PAD_LEFT); // Jadi 00012
        $mqttTopic = Str::slug($device->device_model) . '-' . $deviceId;

        $device->mqtt_topic = $mqttTopic;
        $device->save();
        // Map ruangan dengan group_index
        // Persiapan sinkronisasi pivot

        $ruanganWithGroup = [];
        foreach ($request->ruangan_id as $index => $ruanganId) {
            $ruanganWithGroup[$ruanganId] = ['group_index' => $index];
            // Ambil semua perangkat di ruangan ini
            $perangkatList = Perangkat::where('ruangan_id', $ruanganId)->get();
            foreach ($perangkatList as $perangkat) {
                $topic = $device->mqtt_topic . '/g' . $index . '/' . strtolower($perangkat->tipe);
                $perangkat->update(['topic_mqtt' => $topic]);
            }
        }

        // Attach dengan data pivot tambahan
        $device->ruangans()->attach($ruanganWithGroup);
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
            'ruangan_id' => 'required|array|min:1|max:3',
            'ruangan_id.*' => 'exists:ruangans,id'
        ]);

        $device->update([
            'name' => $request->name,
            'mac_address' => $request->mac_address,
        ]);

        // Siapkan data untuk sync dengan group_index
        $syncData = [];
        foreach ($request->ruangan_id as $index => $ruanganId) {
            $syncData[$ruanganId] = ['group_index' => $index];
        }
        $device->ruangans()->sync($syncData);

        // Update topic_mqtt untuk perangkat-perangkat yang terhubung ke ruangan
        foreach ($request->ruangan_id as $ruanganId) {
            $perangkatList = Perangkat::where('ruangan_id', $ruanganId)->get();

            foreach ($perangkatList as $perangkat) {
                $randomString = Str::random(8);  // menghasilkan string acak dengan panjang 8 karakter

                $topicMqtt = 'esp32/' . Str::slug($device->mac_address) . '/device/' . $device->id . '/ruangan/' . $ruanganId . '/grup/' . array_search($ruanganId, $request->ruangan_id) . '/random/' . $randomString;

                // Update topic_mqtt untuk perangkat
                $perangkat->update([
                    'topic_mqtt' => $topicMqtt
                ]);
            }
        }
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
        $schedules = DB::table('schedules')
            ->where('device_id', $deviceId)
            ->select(['grup_id', 'on_time', 'off_time', 'days'])
            ->get();

        if ($schedules->isNotEmpty()) {
            $finalPayload = $this->encodeSchedulesToBinary($schedules);
            // Kirim ke ESP32 via MQTT
            // $mqttTopic = $device->mqtt_topic;
            $topic = $device->mqtt_topic . '/jadwal/update';
            // $topic = 'esp32/device001/jadwal/update';
            $this->publishMessage2($topic, $finalPayload);
            // $device->update(['sys' => false]);

            return response()->json([
                'status' => 'success',
                // 'message' => $topic,
                'message' => bin2hex($finalPayload),
            ]);
        } else {
            return response()->json([
                'status' => 'skipped',
                'message' => 'Jadwal Kosong',
            ]);
        }
    }

    public function sendManualControl($grupID, $action)
    {
        // Tentukan perintah berdasarkan aksi
        if ($action === 'ON') {
            $cmd = 0x80 + $grupID;  // Perintah hidupkan grup (ON)
        } elseif ($action === 'OFF') {
            $cmd = 0x90 + $grupID;  // Perintah matikan grup (OFF)
        } else {
            return response()->json(['error' => 'Aksi tidak valid'], 400);
        }
        // Buat payload biner (2 byte)
        $payload = pack('CC', $grupID, $cmd);  // C = unsigned char (1 byte)
        $topic = 'esp32-00004/grup/manual';
        $this->publishMessage2($topic, $payload);  // ✅ Kirim biner

        return response()->json(['success' => 'Perintah berhasil dikirim']);
    }



    public function publishMessage($topic, $message)
    {
        $mqtt = MQTT::connection();
        $mqtt->publish($topic, $message);
        $mqtt->disconnect();
    }


    public function jadwalUploadPage(Device $device)
    {
        $ruanganNames = $device->ruangans->pluck('name');
        $jadwals = JadwalRuangan::whereIn('ruangan', $ruanganNames)->get();
        $groupMap = $device->ruangans()->pluck('device_ruangans.group_index', 'name')->toArray();
        // dd($groupMap);
        return view('devices.upload-jadwal', [
            'device' => $device,
            'jadwals' => $jadwals,
            'groupMap' => $groupMap
        ]);
    }


    public function uploadToEsp32(Device $device)
    {
        $ruangans = $device->ruangans->pluck('name');
        $groupMap = $device->ruangans()->pluck('device_ruangans.group_index', 'name')->toArray();
        $jadwals = JadwalRuangan::whereIn('ruangan', $ruangans)->get();
        $grouped = [];
        foreach ($jadwals as $jadwal) {
            $key = $jadwal->on_time . '-' . $jadwal->off_time . '-' . ($groupMap[$jadwal->ruangan] ?? 0);
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'grup_id' => $groupMap[$jadwal->ruangan] ?? 0,
                    'on_time' => $this->timeToMinutes($jadwal->on_time),
                    'off_time' => $this->timeToMinutes($jadwal->off_time),
                    'days' => 0
                ];
            }
            // Set bit sesuai hari
            $grouped[$key]['days'] |= (1 << $jadwal->days);
        }

        $data = array_values($grouped);
        // return respon se()->json($data);
        // dd($device->mqtt_topic);

        if (!empty($data)) {
            $finalPayload = $this->encodeJadwalToBinary($data);
            $topic = $device->mqtt_topic . '/jadwal/update';
            // 'message' => bin2hex($finalPayload),
            try {
                $this->publishMessage2($topic, $finalPayload);
                return redirect()->route('device.jadwalUpload', $device->id)
                    ->with('success', bin2hex($finalPayload) . ' Berhasil di upaload');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal mengirim ke ESP32: ' . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('warning', 'Tidak ada jadwal untuk dikirim.');
        }
    }

    private function timeToMinutes($time)
    {
        [$hour, $minute] = explode(':', $time);
        return (int)$hour * 60 + (int)$minute;
    }
    private function encodeSchedulesToBinary($schedules)
    {
        $binary = '';
        foreach ($schedules as $s) {
            $binary .= chr($s->grup_id);            // 1 byte
            $binary .= chr($s->days);               // 1 byte
            $binary .= pack('n', $s->on_time);      // 2 byte (big endian)
            $binary .= pack('n', $s->off_time);     // 2 byte (big endian)
        }
        return $binary; // binary string ready to send
    }
    private function encodeJadwalToBinary($schedules)
    {
        $binary = '';
        foreach ($schedules as $s) {
            $binary .= chr($s['grup_id']);
            $binary .= chr($s['days']);
            $binary .= pack('n', $s['on_time']);
            $binary .= pack('n', $s['off_time']);
        }
        return $binary; // binary string ready to send
    }
    public function publishMessage2($topic, $message)
    {
        // Ambil konfigurasi dari file config/mqtt.php
        $host = '103.133.56.181';
        $port = '9381';
        $clientId = 'php-mqtt-client';
        $username = 'puskomnet';
        $password = 'puskomnet123';

        try {
            // Membuat instance MqttClient
            $mqtt = new MqttClient($host, $port, $clientId);

            // Menyiapkan pengaturan koneksi dengan otentikasi
            $connectionSettings = (new ConnectionSettings)
                ->setKeepAliveInterval(60)
                ->setUsername($username)  // Menambahkan username
                ->setPassword($password); // Menambahkan password
            // Mencoba untuk melakukan koneksi
            $mqtt->connect($connectionSettings);
            $mqtt->publish($topic, $message, 0);
            // $mqtt->publish('test/topic', $finalPayload, 0, false);
            // $mqtt->publish($topic, $message, 0);
            // Jika koneksi berhasil
            // return response()->json(['status' => 'success', 'message' => 'Berhasil subscribe ke topic ']);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan dalam koneksi
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke broker MQTT: ' . $e->getMessage()
            ]);
        }
    }
}
