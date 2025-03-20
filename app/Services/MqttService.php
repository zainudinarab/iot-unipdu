<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\ConnectionException;

class MqttService
{
    protected $client;

    public function __construct()
    {
        $host = '103.133.56.181';
        $port = '9381';
        $clientId = 'php-mqtt-client';
        $username = 'puskomnet';
        $password = 'puskomnet123';

        $this->client = new MqttClient($host, $port, $clientId);

        $connectionSettings = (new ConnectionSettings)
            ->setKeepAliveInterval(60)
            ->setUsername($username)
            ->setPassword($password);

        // Connect to the broker
        $this->client->connect($connectionSettings);
    }

    // Publish a message to a specific topic
    public function publish(string $topic, string $message)
    {
        // Publishing a message with QoS 0 (at most once)
        $this->client->publish($topic, $message, 0);
    }

    // Subscribe to a topic and handle incoming messages
    public function subscribe(string $topic)
    {
        $this->client->subscribe($topic, function ($topic, $message) {
            // Handle the received message for this topic
            echo "Received message: $message on topic: $topic\n";
        });
    }

    // Disconnect from the MQTT broker
    public function disconnect()
    {
        $this->client->disconnect();
    }
}
