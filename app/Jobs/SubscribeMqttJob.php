<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpMqtt\Client\Facades\MQTT;

class SubscribeMqttJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $topic;

    public function __construct($topic)
    {
        $this->topic = $topic;
    }

    public function handle()
    {
        $mqtt = MQTT::connection();
        $mqtt->subscribe($this->topic, function ($topic, $message) {
            \Log::info("Received message on topic [$topic]: $message");
        });

        $mqtt->loop(true); // Loop untuk terus mendengarkan pesan
    }
}
