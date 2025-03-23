<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use PhpMqtt\Client\Facades\MQTT;
use App\Models\Perangkat;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class PerangkatController extends Controller
{
    public function index()
    {
        $perangkat = Perangkat::all(); // Mengambil semua data perangkat
        return response()->json($perangkat);
        // return response()->json(Perangkat::with('kelas.lantai.gedung')->get());
        // yang baru

    }

    // perangan by ruangan
    public function perangkatGrupRuangan(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
        ]);

        $perangkats = Perangkat::where('ruangan_id', $request->ruangan_id)->get();
        return response()->json($perangkats);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'ruangan_id' => 'required|exists:ruangans,id',
                'nama' => 'required|string',
                'kategori' => 'required|string',
                'tipe' => 'required|string',
                'data_tambahan' => 'nullable|array',
            ]);
            $lastNumber = Perangkat::where('ruangan_id', $validated['ruangan_id'])
                ->where('kategori', $validated['kategori'])
                ->max('nomor_urut');
            $nomorUrut = $lastNumber ? $lastNumber + 1 : 1;
            $perangkat = Perangkat::create([
                'ruangan_id' => $validated['ruangan_id'],
                'tipe' => $validated['tipe'],
                'nama' => $validated['nama'],
                'kategori' => $validated['kategori'],
                'nomor_urut' => $nomorUrut,
                'topic_mqtt' => '', // Mengosongkan nilai ini karena kita akan mengisi setelah
                'status' => false,
                'data_tambahan' => $validated['data_tambahan']
            ]);
            // Mengupdate kolom mqtt_topic menggunakan accessor
            $perangkat->topic_mqtt = $perangkat->mqttTopic; // Memanggil accessor untuk mendapatkan mqtt_topic yang dihasilkan
            $perangkat->save();

            return response()->json([
                'success' => true,
                'message' => 'Perangkat berhasil ditambahkan.',
                'data' => $perangkat
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        return response()->json(Perangkat::with('kelas.lantai.gedung')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        // validate
        $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'tipe' => 'required|string',
            'nama' => 'required|string',
            'topic_mqtt' => 'required|string',
            'data_tambahan' => 'nullable|array',
        ]);

        $perangkat = Perangkat::findOrFail($id);
        $perangkat->update($request->all());
        return response()->json($perangkat);
    }

    public function destroy($id)
    {
        Perangkat::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:0,1', // Hanya menerima 0 atau 1
        ]);
        $perangkat = Perangkat::findOrFail($id);
        $perangkat->status = $request->status;
        $perangkat->save();
        //public

        $topic = $perangkat->topic_mqtt;
        $mqtt = MQTT::connection();
        $mqtt->publish($topic, $request->status);
        $mqtt->disconnect();
        // return response()->json([
        //     'message' => 'Status perangkat berhasil diperbarui.',
        //     'perangkat' => $perangkat,
        // ]);
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
    public function perangkatByRuangan()
    {
        $ruangans = Ruangan::with(['lantai', 'gedung'])->get();
        $ruangans->transform(function ($ruangans) {
            // Kelompokkan perangkat relay
            $ruangans->perangkat_relay = $ruangans->perangkats->filter(function ($perangkat) {
                return trim($perangkat->tipe) === 'relay' || trim($perangkat->tipe) === 'ir'; // Trim untuk menghindari spasi ekstra
            });
            // Kelompokkan perangkat sensor
            $ruangans->perangkat_sensor = $ruangans->perangkats->filter(function ($perangkat) {
                return trim($perangkat->tipe) === 'sensor'; // Trim untuk menghindari spasi ekstra
            });
            // Hapus perangkat yang tidak dikelompokkan (opsional)
            unset($ruangans->lantai, $ruangans->perangkats); // Menghapus perangkat yang mencakup semua
            return $ruangans;
        });
        return response()->json($ruangans);
    }
}
