<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

use App\Http\Controllers\API\{
    GedungController,
    RuanganController,
    PerangkatController,
    RFIDCardController,
};

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [GedungController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [GedungController::class, 'dashboard']);
});

Route::patch('perangkat/{id}/status', [PerangkatController::class, 'updateStatus']);
Route::apiResource('gedungs', GedungController::class);

Route::apiResource('perangkats', PerangkatController::class);
Route::apiResource('rfid-cards', RFIDCardController::class);
Route::apiResource('ruangans', RuanganController::class);
// perangkat group by Ruangan
Route::get('/perangkat-by-ruangan', [PerangkatController::class, 'perangkatByRuangan']);
// getlantaai
Route::get('/lantais', [GedungController::class, 'getLantai']);
// perangkatGrupRuangan
Route::get('/perangkat-grup-ruangan', [PerangkatController::class, 'perangkatGrupRuangan']);

// Di routes/api.php
Route::get('/perangkat-topics', [PerangkatController::class, 'mqttTopics']);
// updateStatusFromNodejs
Route::post('/update-status-from-nodejs', [PerangkatController::class, 'updateStatusFromNodejs']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
