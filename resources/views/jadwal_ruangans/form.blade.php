@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ $jadwal->exists ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h2>

        <form method="POST"
            action="{{ $jadwal->exists ? route('jadwal-ruangans.update', $jadwal->id) : route('jadwal-ruangans.store') }}">
            @csrf
            @if ($jadwal->exists)
                @method('PUT')
            @endif

            <div class="mb-3">
                <label>Ruangan</label>
                <input type="text" name="ruangan" class="form-control" value="{{ old('ruangan', $jadwal->ruangan) }}"
                    required>
            </div>

            <div class="mb-3">
                <label>Jam ON</label>
                <input type="time" name="on_time" class="form-control" value="{{ old('on_time', $jadwal->on_time) }}"
                    required>
            </div>

            <div class="mb-3">
                <label>Jam OFF</label>
                <input type="time" name="off_time" class="form-control" value="{{ old('off_time', $jadwal->off_time) }}"
                    required>
            </div>

            <div class="mb-3">
                <label>Hari</label>
                <select name="days" class="form-control" required>
                    <option value="" disabled
                        {{ old('days', $jadwal->days) === null && $jadwal->days !== 0 ? 'selected' : '' }}>Pilih Hari
                    </option>

                    @php
                        $dayOptions = [
                            0 => 'Minggu',
                            1 => 'Senin',
                            2 => 'Selasa',
                            3 => 'Rabu',
                            4 => 'Kamis',
                            5 => 'Jumat',
                            6 => 'Sabtu',
                        ];
                    @endphp
                    @foreach ($dayOptions as $key => $label)
                        <option value="{{ $key }}"
                            {{ (string) old('days', $jadwal->days) === (string) $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>



            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('jadwal-ruangans.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection
