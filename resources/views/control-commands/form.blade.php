@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>{{ isset($command) ? 'Edit' : 'Tambah' }} Perintah IR</h3>

        <form
            action="{{ isset($command) ? route('control-commands.update', $command->id) : route('control-commands.store') }}"
            method="POST">
            @csrf
            @if (isset($command))
                @method('PUT') <!-- Gunakan PUT jika editing -->
            @endif

            <input type="hidden" name="device_control_id"
                value="{{ old('device_control_id', $command->device_control_id ?? $deviceControl->id) }}">

            <div class="mb-3">
                <label for="command_type" class="form-label">Tipe Perintah</label>
                <select name="command_type" id="command_type" class="form-select" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="ON"
                        {{ old('command_type', $command->command_type ?? '') == 'ON' ? 'selected' : '' }}>ON</option>
                    <option value="OFF"
                        {{ old('command_type', $command->command_type ?? '') == 'OFF' ? 'selected' : '' }}>OFF</option>
                </select>
            </div>

            <textarea name="data" id="data" class="form-control" rows="5" required>{{ old('data', isset($command->data) ? implode(',', $command->data) : '') }}</textarea>


            <button type="submit" class="btn btn-{{ isset($command) ? 'primary' : 'success' }}">
                {{ isset($command) ? '💾 Update' : '💾 Simpan' }}
            </button>

            <a href="{{ route('control-commands.index', ['device_control' => $command->deviceControl->id ?? $deviceControl->id]) }}"
                class="btn btn-secondary">↩️ Kembali ke Daftar Perintah</a>

        </form>
    </div>
@endsection
