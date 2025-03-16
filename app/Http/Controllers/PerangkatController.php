<?php

namespace App\Http\Controllers;

use App\Models\Perangkat;
use App\Models\StatusPerangkat;
use App\Services\MqttService;
use Illuminate\Http\Request;

class PerangkatController extends Controller
{
    protected $mqttService;

    public function __construct(MqttService $mqttService)
    {
        $this->mqttService = $mqttService;
    }

    public function toggleStatus($id)
    {
        $perangkat = Perangkat::findOrFail($id);
        $status = $perangkat->status->status === 'ON' ? 'OFF' : 'ON';

        // Kirim perintah ke MQTT
        $this->mqttService->publish($perangkat->topik_mqtt, $status);

        // Simpan status ke database
        StatusPerangkat::updateOrCreate(
            ['perangkat_id' => $id],
            ['status' => $status]
        );

        return response()->json(['status' => 'success', 'message' => 'Status updated']);
    }
    // Menampilkan daftar perangkat
    public function index()
    {
        $perangkat = Perangkat::with('ruangan')->get();
        return response()->json($perangkat);
    }

    // Menambahkan perangkat baru
    public function store(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:ruangan,id',
            'nama' => 'required|string|max:255',
            'topik_mqtt' => 'required|string|max:255',
        ]);

        $perangkat = Perangkat::create([
            'ruangan_id' => $request->ruangan_id,
            'nama' => $request->nama,
            'topik_mqtt' => $request->topik_mqtt,
        ]);

        return response()->json($perangkat, 201);
    }

    // Menampilkan detail perangkat berdasarkan ID
    public function show($id)
    {
        $perangkat = Perangkat::with('ruangan')->findOrFail($id);
        return response()->json($perangkat);
    }

    // Mengupdate data perangkat
    public function update(Request $request, $id)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:ruangan,id',
            'nama' => 'required|string|max:255',
            'topik_mqtt' => 'required|string|max:255',
        ]);

        $perangkat = Perangkat::findOrFail($id);
        $perangkat->update([
            'ruangan_id' => $request->ruangan_id,
            'nama' => $request->nama,
            'topik_mqtt' => $request->topik_mqtt,
        ]);

        return response()->json($perangkat);
    }

    // Menghapus perangkat
    public function destroy($id)
    {
        $perangkat = Perangkat::findOrFail($id);
        $perangkat->delete();

        return response()->json(['message' => 'Perangkat deleted successfully']);
    }
}
