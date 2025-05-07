@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Tambah Data Raw IR untuk AC - Device: {{ $device->name }} (AC {{ $acIndex + 1 }})</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form untuk menambah data raw IR -->
        <form action="{{ route('device.ir.store', ['device' => $device->id, 'acIndex' => $acIndex]) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="rawDataOn" class="form-label">Raw Data ON (Hexadecimal Array)</label>
                <textarea name="rawDataOn" id="rawDataOn" class="form-control" rows="5"
                    placeholder="Contoh: 2976, 8964, 504, 490, ...">{!! old('rawDataOn') ?? (isset($deviceIr) ? implode(', ', $deviceIr->rawDataOn) : '') !!}</textarea>

                @error('rawDataOn')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="rawDataOff" class="form-label">Raw Data OFF (Hexadecimal Array)</label>
                <textarea name="rawDataOff" id="rawDataOff" class="form-control" rows="5"
                    placeholder="Contoh: 2976, 8964, 504, 490, ...">{!! old('rawDataOff') ?? (isset($deviceIr) ? implode(', ', $deviceIr->rawDataOff) : '') !!}</textarea>

                @error('rawDataOff')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Tambah Data</button>
        </form>
        <!-- Tombol untuk update data ke MQTT -->
        <form action="{{ route('device.ir.updateToMqtt', ['device' => $device->id, 'acIndex' => $acIndex]) }}"
            method="POST">
            @csrf
            <button type="submit" class="btn btn-success mt-3">Update Data ke MQTT</button>
        </form>

    </div>
@endsection
