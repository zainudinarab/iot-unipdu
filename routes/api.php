<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

use App\Http\Controllers\API\{
    GedungController,
    LantaiController,
    RuanganController,
    KelasController,
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
Route::apiResource('lantais', LantaiController::class);
Route::apiResource('kelas', KelasController::class);
Route::apiResource('perangkats', PerangkatController::class);
Route::apiResource('rfid-cards', RFIDCardController::class);
Route::apiResource('ruangans', RuanganController::class);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
