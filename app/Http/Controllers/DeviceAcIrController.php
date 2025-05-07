<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceIr;
use Illuminate\Http\Request;
use PhpMqtt\Client\Facades\MQTT;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;


class DeviceAcIrController extends Controller
{
    public function createForm($deviceId, $acIndex)
    {
        $device = Device::findOrFail($deviceId);

        // Mencari data IR berdasarkan device_id dan ac_index
        $deviceIr = DeviceIr::where('device_id', $device->id)
            ->where('ac_index', $acIndex)
            ->first();

        // Menyediakan data untuk form
        // dd($deviceIr);
        return view('devices.ir.create', compact('device', 'acIndex', 'deviceIr'));
        // $device = Device::findOrFail($deviceId);
        // return view('devices.ir.create', compact('device', 'acIndex'));
    }


    public function store(Request $request, Device $device, $acIndex)
    {
        // Validasi input untuk memastikan rawDataOn dan rawDataOff adalah string yang valid
        $request->validate([
            'rawDataOn' => 'required|string',  // Harus berupa string
            'rawDataOff' => 'required|string', // Harus berupa string
        ]);

        // Proses input rawDataOn dan rawDataOff
        // Menggunakan explode untuk memisahkan string berdasarkan koma dan array_map untuk mengubah setiap nilai menjadi integer
        $rawDataOn = array_map('intval', explode(',', $request->rawDataOn));
        $rawDataOff = array_map('intval', explode(',', $request->rawDataOff));

        // Update atau buat entri baru pada tabel device_irs
        DeviceIr::updateOrCreate(
            ['device_id' => $device->id, 'ac_index' => $acIndex], // Kunci pencarian: device_id dan ac_index
            [
                'rawDataOn' => $rawDataOn, // Menyimpan data ON sebagai array integer
                'rawDataOff' => $rawDataOff, // Menyimpan data OFF sebagai array integer
            ]
        );

        // Redirect dengan pesan sukses setelah data berhasil disimpan
        return redirect()->route('devices.index')->with('success', 'Data IR berhasil disimpan');
    }

    // Menampilkan form untuk input/update data raw IR
    public function edit($deviceId, $acIndex)
    {
        // Mendapatkan device berdasarkan id
        $device = Device::findOrFail($deviceId);

        // Mencari data IR berdasarkan device_id dan ac_index
        $deviceIr = DeviceIr::where('device_id', $device->id)
            ->where('ac_index', $acIndex)
            ->first();

        // Menyediakan data untuk form
        return view('device.ir.edit', compact('device', 'acIndex', 'deviceIr'));
    }

    // Mengupdate data raw IR untuk AC
    public function update(Request $request, $deviceId, $acIndex)
    {
        $request->validate([
            'rawDataOn' => 'required|array',  // Data raw IR untuk ON
            'rawDataOff' => 'required|array', // Data raw IR untuk OFF
        ]);

        $device = Device::findOrFail($deviceId);

        // Cari data IR untuk AC yang sesuai dan update
        $acData = DeviceIr::updateOrCreate(
            ['device_id' => $deviceId, 'ac_index' => $acIndex],
            [
                'rawDataOn' => $request->rawDataOn,
                'rawDataOff' => $request->rawDataOff
            ]
        );

        return redirect()->route('device.ac.edit', ['deviceId' => $deviceId, 'acIndex' => $acIndex])
            ->with('success', 'Data IR berhasil diperbarui');
    }


    public function updateToMqtt($deviceId, $acIndex)
    {
        $deviceIr = DeviceIr::where('device_id', $deviceId)
            ->where('ac_index', $acIndex)
            ->firstOrFail();

        $onHex = $this->encodeIrArrayToHex($deviceIr->rawDataOn);
        $offHex = $this->encodeIrArrayToHex($deviceIr->rawDataOff);

        $this->sendHexAsChunks($onHex, $acIndex, true);
        $this->sendHexAsChunks($offHex, $acIndex, false);

        return redirect()->back()->with('success', 'Data berhasil diupdate ke MQTT!');
    }

    function encodeIrArrayToHex(array $data): string
    {
        return collect($data)
            ->map(function ($value) {
                return strtoupper(str_pad(dechex($value), 4, '0', STR_PAD_LEFT));
            })
            ->implode('');
    }
    function sendHexAsChunks(string $hex, int $acIndex, bool $isOn)
    {
        $chunkSize = 168; // jumlah karakter per chunk (bisa ubah sesuai kebutuhan)
        $chunks = str_split($hex, $chunkSize);
        $totalParts = count($chunks);

        for ($i = 0; $i < $totalParts; $i++) {
            $payload = [
                'part' => $i,
                'total' => $totalParts,
                'data' => $chunks[$i],
                'ac' => $acIndex,
                'is_on' => $isOn
            ];

            $jsonPayload = json_encode($payload);
            $topic = 'esp32-00004/ac/update/' . ($isOn ? 'on' : 'off') . '/' . $acIndex;

            $this->publishMessage2($topic, $jsonPayload);

            usleep(1000000); // Delay kecil antar publish (100ms), agar ESP32 bisa catch up
        }
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

            $mqtt = new MqttClient($host, $port, $clientId);
            $connectionSettings = (new ConnectionSettings)
                ->setKeepAliveInterval(60)
                ->setUsername($username)  // Menambahkan username
                ->setPassword($password); // Menambahkan password
            // Mencoba untuk melakukan koneksi
            $mqtt->connect($connectionSettings);
            $mqtt->publish($topic, $message, 0);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke broker MQTT: ' . $e->getMessage()
            ]);
        }
    }
}
