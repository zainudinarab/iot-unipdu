<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;

class SubscribeMqttTopic extends Command
{
    protected $signature = 'mqtt:subscribe {topic}';
    protected $description = 'Subscribe to an MQTT topic';

    public function handle()
    {
        $topic = $this->argument('topic');

        $mqtt = MQTT::connection();
        $mqtt->subscribe($topic, function ($topic, $message) {
            $this->info("Received message on topic [$topic]: $message");
        });

        $mqtt->loop(true); // Loop untuk terus mendengarkan pesan
    }
}
