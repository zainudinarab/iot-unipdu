<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MqttController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\PerangkatController;
use App\Http\Controllers\StatusPerangkatController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DeviceAcIrController;
use App\Http\Controllers\JadwalRuanganController;
use App\Http\Controllers\DeviceControlController;
use App\Http\Controllers\ControlCommandController;





Route::get('/', function () {
    return view('welcome');
});

Route::get('/jadwal-ruangans', [JadwalRuanganController::class, 'index'])->name('jadwal-ruangans.index');
Route::get('/jadwal-ruangans/create', [JadwalRuanganController::class, 'create'])->name('jadwal-ruangans.create');
Route::post('/jadwal-ruangans', [JadwalRuanganController::class, 'store'])->name('jadwal-ruangans.store');
Route::get('/jadwal-ruangans/{id}/edit', [JadwalRuanganController::class, 'edit'])->name('jadwal-ruangans.edit');
Route::put('/jadwal-ruangans/{id}', [JadwalRuanganController::class, 'update'])->name('jadwal-ruangans.update');
Route::delete('/jadwal-ruangans/{id}', [JadwalRuanganController::class, 'destroy'])->name('jadwal-ruangans.destroy');



Route::get('/jadwal/import', [JadwalRuanganController::class, 'importFromJsonUrl'])->name('jadwal.import');





// Menampilkan form untuk input data raw IR
Route::get('/devices/{deviceId}/ir/{acIndex}/create', [DeviceAcIrController::class, 'createForm'])->name('device.ir.create');
Route::post('/devices/{device}/ir/{acIndex}', [DeviceAcIrController::class, 'store'])->name('device.ir.store');
Route::post('/device/{device}/ir/update-mqtt/{acIndex}', [DeviceAcIrController::class, 'updateToMqtt'])->name('device.ir.updateToMqtt');
// Menyimpan data raw IR yang dimasukkan
// Route::post('/devices/{deviceId}/ir/{acIndex}/create', [DeviceAcIrController::class, 'create'])->name('device.ir.create');


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



Route::resource('devices', DeviceController::class);
Route::resource('schedules', ScheduleController::class);
Route::post('/devices/sync/{deviceId}', [DeviceController::class, 'syncSchedules'])->name('devices.sync');
Route::get('/devices/sync/{deviceId}', [DeviceController::class, 'syncSchedules'])->name('get   devices.sync');
Route::post('/control/grup/{grupID}/{action}', [DeviceController::class, 'sendManualControl']);
Route::get('/device/{device}/jadwal-upload', [DeviceController::class, 'jadwalUploadPage'])->name('device.jadwalUpload');
Route::post('/device/{device}/upload-jadwal', [DeviceController::class, 'uploadToEsp32'])->name('device.uploadToEsp32');
Route::get('/device/{device}/management', [DeviceController::class, 'management'])->name('device.management');
// Tampilkan form tambah kontrol
Route::get('/device/{device}/control/create/{ruangan}', [DeviceControlController::class, 'create'])->name('device.control.create');
Route::post('/device/{device}/control', [DeviceControlController::class, 'store'])->name('device.control.store');
Route::delete('device-controls/{deviceControl}', [DeviceControlController::class, 'destroy'])->name('device-controls.destroy');
// Route untuk menampilkan halaman manajemen kontrol perangkat



Route::prefix('control-commands')->name('control-commands.')->group(function () {
    // Menampilkan daftar perintah (khusus 1 device_control_id)
    Route::get('/{device_control}/index', [ControlCommandController::class, 'showIndexForm'])->name('index');
    Route::get('/create/{device_control}', [ControlCommandController::class, 'create'])->name('create');
    Route::post('/', [ControlCommandController::class, 'store'])->name('store');
    Route::get('/{command}/edit', [ControlCommandController::class, 'edit'])->name('edit');
    Route::put('/{command}', [ControlCommandController::class, 'update'])->name('update');
    Route::delete('/{command}', [ControlCommandController::class, 'destroy'])->name('destroy');
    Route::get('/{command}/test', [ControlCommandController::class, 'test'])->name('test');
    Route::get('/{device_control}/update-code', [ControlCommandController::class, 'updateCode'])->name('update-code');
});
