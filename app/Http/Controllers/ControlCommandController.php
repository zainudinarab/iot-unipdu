<?php

namespace App\Http\Controllers;

use App\Models\ControlCommand;
use App\Models\DeviceControl;
use Illuminate\Http\Request;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class ControlCommandController extends Controller
{
    public function showIndexForm(DeviceControl $deviceControl)
    {
        // Ambil semua perintah yang terkait dengan device_control_id
        $existingCommands = ControlCommand::where('device_control_id', $deviceControl->id)->get();

        // Kembalikan ke view dengan mengirimkan data perintah yang ada
        return view('control-commands.index', compact('deviceControl', 'existingCommands'));
    }

    public function create(Request $request, DeviceControl $deviceControl)
    {
        // Menampilkan form untuk tambah perintah dengan data deviceControl yang sudah dipilih
        return view('control-commands.form', compact('deviceControl'));
    }




    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_control_id' => 'required|exists:device_controls,id',
            'command_type' => 'required|in:ON,OFF',
            'data' => 'required|string',
        ]);
        // Mencari apakah sudah ada perintah dengan device_control_id dan command_type yang sama
        $existingCommand = ControlCommand::where('device_control_id', $validated['device_control_id'])
            ->where('command_type', $validated['command_type'])
            ->first();

        if ($existingCommand) {
            // Jika sudah ada, lakukan update pada data yang sudah ada
            $existingCommand->update([
                'data' => $validated['data'],
            ]);

            return redirect()->route('control-commands.index', ['device_control' => $validated['device_control_id']])
                ->with('success', 'DataRow untuk perintah ' . $validated['command_type'] . ' berhasil ditambahkan!');
        } else {
            // Jika belum ada, buat data baru
            ControlCommand::create([
                'device_control_id' => $validated['device_control_id'],
                'command_type' => $validated['command_type'],
                'data' => $validated['data'],
            ]);

            return redirect()->route('control-commands.index', ['device_control' => $validated['device_control_id']])
                ->with('success', 'DataRow untuk perintah ' . $validated['command_type'] . ' berhasil ditambahkan!');
        }
    }

    public function edit(ControlCommand $command)
    {
        // Mendapatkan deviceControl terkait dengan perintah (command)
        $deviceControl = $command->deviceControl;

        // Menampilkan form edit perintah dengan data perintah yang sudah ada
        return view('control-commands.form', compact('command', 'deviceControl'));
    }
    public function update(Request $request, ControlCommand $command)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'command_type' => 'required|in:ON,OFF',
            'data' => 'required|string',
        ]);

        // Update data perintah yang ada
        $command->update([
            'command_type' => $validated['command_type'],
            'data' => $validated['data'],
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('control-commands.index', ['device_control' => $command->device_control_id])
            ->with('success', 'Perintah ' . $validated['command_type'] . ' berhasil diperbarui!');
    }

    public function destroy(ControlCommand $command)
    {
        // Simpan id device control untuk redirect
        $deviceControlId = $command->device_control_id;

        // Hapus perintah
        $command->delete();

        // Redirect ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('control-commands.index', $deviceControlId)
            ->with('success', 'Perintah berhasil dihapus');
    }

    public function updateCode(DeviceControl $deviceControl)
    {
        // Ambil perintah ON dan OFF
        $commands = ControlCommand::where('device_control_id', $deviceControl->id)
            ->whereIn('command_type', ['ON', 'OFF'])
            ->get();

        $onCommand = $commands->where('command_type', 'ON')->first();
        $offCommand = $commands->where('command_type', 'OFF')->first();

        // Validasi: ON dan OFF harus ada
        if (!$onCommand || !$offCommand) {
            return redirect()->route('control-commands.index', ['device_control' => $deviceControl->id])
                ->with('error', 'Perintah ON dan OFF harus ada sebelum mengirim ke MQTT.');
        }

        // Encode data IR ke Hex
        try {
            $onHex = $this->encodeIrArrayToHex($onCommand->data);
            $offHex = $this->encodeIrArrayToHex($offCommand->data);
        } catch (\Exception $e) {
            return redirect()->route('control-commands.index', ['device_control' => $deviceControl->id])
                ->with('error', 'Gagal mengubah data IR ke hex: ' . $e->getMessage());
        }

        // Kirim ke MQTT sebagai chunk
        $acIndex = $deviceControl->index;

        try {
            $this->sendHexAsChunks($onHex, $acIndex, true);  // true = ON
            $this->sendHexAsChunks($offHex, $acIndex, false); // false = OFF
        } catch (\Exception $e) {
            return redirect()->route('control-commands.index', ['device_control' => $deviceControl->id])
                ->with('error', 'Gagal mengirim data ke MQTT: ' . $e->getMessage());
        }

        return redirect()->route('control-commands.index', ['device_control' => $deviceControl->id])
            ->with('success', 'Perintah ON dan OFF berhasil dikirim ke MQTT!');
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
            $topic = 'esp32-00004/ir/update/' . $acIndex . '/' . ($isOn ? 'on' : 'off');


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
