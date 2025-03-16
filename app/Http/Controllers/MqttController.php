<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use PhpMqtt\Client\Facades\MQTT;
use PhpMqtt\Client\Facades\MQTT;

class MqttController extends Controller
{

    // Method untuk publish pesan MQTT
    public function publishMessage($topic, $message)
    {

        $mqtt = MQTT::connection();
        $mqtt->publish($topic, $message);
        $mqtt->disconnect();

        return response()->json([
            'status' => 'success',
            'message' => 'Message published to topic: ' . $topic,
            'message2' => $message
        ]);
    }
    public function subscribeToTopic($topic)
    {
        $mqtt = MQTT::connection();
        $mqtt->subscribe($topic, function ($topic, $message) {
            echo "Received message on topic [$topic]: $message\n";
        });
        $mqtt->loop(true); // Loop untuk terus mendengarkan pesan

        return response()->json([
            'status' => 'success',
            'message' => 'Subscribed to topic: ' . $topic,
        ]);
    }
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
}
