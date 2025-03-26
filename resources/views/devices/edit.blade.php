@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Device</h2>
        <form action="{{ route('devices.update', $device->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Nama Device</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $device->name) }}" required>
            </div>

            <div class="mb-3">
                <label for="mac_address" class="form-label">MAC Address</label>
                <input type="text" name="mac_address" class="form-control"
                    value="{{ old('mac_address', $device->mac_address) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Ruangan (Maksimal 2)</label>
                <select name="ruangan_id[]" class="form-control" multiple required>
                    @foreach ($ruangans as $ruangan)
                        <option value="{{ $ruangan->id }}"
                            {{ in_array($ruangan->id, $device->ruangans->pluck('id')->toArray()) ? 'selected' : '' }}>
                            {{ $ruangan->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="mqtt_topic" class="form-label">MQTT Topic</label>
                <input type="text" class="form-control" value="{{ $device->mqtt_topic }}" disabled>
                <small class="text-muted">Topic MQTT tidak bisa diubah.</small>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('devices.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
