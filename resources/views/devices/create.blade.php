@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Tambah Device</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('devices.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nama Device</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="mac_address" class="form-label">MAC Address</label>
                <input type="text" name="mac_address" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="device_type" class="form-label">Tipe Perangkat</label>
                <input type="text" name="device_type" class="form-control" placeholder="Contoh: ESP32" required>
            </div>

            <div class="mb-3">
                <label for="device_model" class="form-label">Model Perangkat</label>
                <input type="text" name="device_model" class="form-control" placeholder="Contoh: ESP32-S3" required>
            </div>

            <div class="mb-3">
                <label for="firmware_version" class="form-label">Versi Firmware</label>
                <input type="text" name="firmware_version" class="form-control" placeholder="Contoh: v1.0.0">
            </div>

            <div class="mb-3">
                <label for="ip_address" class="form-label">IP Address</label>
                <input type="text" name="ip_address" class="form-control" placeholder="Contoh: 192.168.1.123">
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Lokasi Perangkat</label>
                <input type="text" name="location" class="form-control" placeholder="Contoh: Lab 1, Ruang Server">
            </div>

            <div class="mb-3">
                <label for="ruangan_id" class="form-label">Pilih Ruangan</label>
                <select name="ruangan_id[]" class="form-control" multiple required>
                    @foreach ($ruangans as $ruangan)
                        <option value="{{ $ruangan->id }}">{{ $ruangan->name }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Maksimal 2 ruangan per device.</small>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>

    </div>
@endsection
