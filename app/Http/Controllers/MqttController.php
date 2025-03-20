<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use PhpMqtt\Client\Facades\MQTT;
use PhpMqtt\Client\Facades\MQTT;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Services\MqttService;
use PhpMqtt\Client\Exceptions\ConnectionException;

class MqttController extends Controller
{
    protected $mqttService;

    public function __construct(MqttService $mqttService)
    {
        $this->mqttService = $mqttService;
    }
    // Method untuk publish pesan MQTT
    // public function publishMessage($topic, $message)
    // {

    //     $mqtt = MQTT::connection();
    //     $mqtt->publish($topic, $message);
    //     $mqtt->disconnect();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Message published to topic: ' . $topic,
    //         'message2' => $message
    //     ]);
    // }
    // public function subscribeToTopic($topic)
    // {
    //     $mqtt = MQTT::connection();
    //     $mqtt->subscribe($topic, function ($topic, $message) {
    //         echo "Received message on topic [$topic]: $message\n";
    //     });
    //     $mqtt->loop(true); // Loop untuk terus mendengarkan pesan

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Subscribed to topic: ' . $topic,
    //     ]);
    // }
    // publish
    public function publish()
    {
        $mqtt = MQTT::connection();
        $mqtt->publish('some/topic', 'Hello World!');
        $mqtt->disconnect();

        return response()->json([
            'status' => 'success',
            'message' => 'Message published to topic: topic',
        ]);
    }

    // subscribe
    public function subscribe()
    {
        $mqtt = MQTT::connection();
        $mqtt->subscribe('some/topic', function (string $topic, string $message) {
            echo sprintf('Received QoS level 1 message on topic [%s]: %s', $topic, $message);
        }, 1);
        $mqtt->loop(true);
    }
    public function testConnection()
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
            $mqtt->publish('some/topic', 'Hello Wxxxxxxxxxxxxxxxorld!', 0);
            // Jika koneksi berhasil

            $this->mqttService->subscribe('some/topic');

            return response()->json(['status' => 'success', 'message' => 'Berhasil subscribe ke topic ']);
            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Koneksi ke broker MQTT berhasil dengan otentikasi!'
            // ]);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan dalam koneksi
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke broker MQTT: ' . $e->getMessage()
            ]);
        }
    }
    public function publishMessage(Request $request)
    {
        $topic = $request->input('topic');
        $message = $request->input('message');

        if (!$topic || !$message) {
            return response()->json(['error' => 'Topic dan message diperlukan.'], 400);
        }

        $host = '103.133.56.181';
        $port = '9381';
        $clientId = 'php-mqtt-client';
        $username = 'puskomnet';
        $password = 'puskomnet123';

        // Buat instance dari MqttClient
        $client = new MqttClient($host, $port, $clientId);
        $connectionSettings = (new ConnectionSettings)
            ->setKeepAliveInterval(60)
            ->setUsername($username)
            ->setPassword($password);

        try {
            // Membuat instance MqttClient
            $mqtt = new MqttClient($host, $port, $clientId);
            $connectionSettings = (new ConnectionSettings)
                ->setKeepAliveInterval(60)
                ->setUsername($username)  // Menambahkan username
                ->setPassword($password); // Menambahkan password
            $mqtt->connect($connectionSettings);
            $mqtt->publish($topic, $message, 0);
            return response()->json([
                'status' => 'success',
                'message' => 'Koneksi ke broker MQTT berhasil dengan otentikasi!'
            ]);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan dalam koneksi
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke broker MQTT: ' . $e->getMessage()
            ]);
        }
    }

    // Endpoint untuk subscribe ke topic MQTT


    // Endpoint untuk disconnect dari broker MQTT
    public function disconnect()
    {
        $this->mqttService->disconnect();
        return response()->json(['status' => 'success', 'message' => 'Terputus dari broker MQTT.']);
    }

    public function subscribeToTopic()
    {
        // Topik yang ingin disubscribe
        $topic = 'some/topic'; // Ganti dengan topik yang diinginkan

        // Konfigurasi broker MQTT
        $server = '103.133.56.181';
        $port = '9381';
        $clientId = 'php-mqtt-client';
        $username = 'puskomnet';
        $password = 'puskomnet123';
        // Membuat instansi MqttClient
        $mqtt = new MqttClient($server, $port, $clientId);

        // Set kredensial (username dan password)
        $mqtt->setCredentials($username, $password);

        // Koneksi ke broker MQTT
        $mqtt->connect();

        // Subscribe ke topik
        $mqtt->subscribe('php-mqtt/client/test', function ($topic, $message) {
            echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);
        }, 0);

        // Menjalankan loop untuk menerima pesan
        $mqtt->loop(true);

        // Disconnect setelah loop selesai
        $mqtt->disconnect();

        return response()->json(['message' => 'Subscribed successfully']);
    }

    public function getMqttMessage()
    {
        // Ambil pesan dari session
        $message = session('mqtt_message', 'Tidak ada pesan baru');
        return response()->json(['message' => $message]);
    }
}
