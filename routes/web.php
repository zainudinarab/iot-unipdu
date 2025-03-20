<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MqttController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\PerangkatController;
use App\Http\Controllers\StatusPerangkatController;


Route::get('/', function () {
    return view('welcome');
});

Route::resource('ruangan', RuanganController::class);
Route::resource('perangkat', PerangkatController::class);
Route::resource('status-perangkat', StatusPerangkatController::class);

Route::get('/mqtt/publish/{topic}/{message}', [MqttController::class, 'publishMessage']);
Route::get('/mqtt/subscribe/{topic}', [MqttController::class, 'subscribeToTopic']);
// Route::get('/mqtt/publish', [MqttController::class, 'publish']);
// Route::get('/mqtt/subscribe', [MqttController::class, 'subscribe']);
Route::get('mqtt/test-connection', [MqttController::class, 'testConnection']);
Route::get('mqtt/subscribe', [MqttController::class, 'subscribeToTopic']);
Route::get('mqtt/message', [MqttController::class, 'getMqttMessage']);
