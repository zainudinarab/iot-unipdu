@extends('layouts.app')

@section('content')
    <h4>Jadwal untuk Device: {{ $device->nama }}</h4>
    <div class="d-flex flex-wrap gap-2 my-3">


        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            ⬅️ Kembali
        </a>
    </div>
    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mt-2">{{ session('error') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>Ruangan</th>
                <th>Index Group</th>
                <th>Jam On</th>
                <th>Jam Off</th>
                <th>Hari</th>
            </tr>
        </thead>
        <tbody>
            @php
                $namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            @endphp
            @foreach ($jadwals as $jadwal)
                <tr>
                    <td>{{ $jadwal->ruangan }}</td>
                    <td>{{ $groupMap[$jadwal->ruangan] ?? '-' }}</td>
                    <td>{{ $jadwal->on_time }}</td>
                    <td>{{ $jadwal->off_time }}</td>
                    <td>{{ $namaHari[$jadwal->days] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <form method="POST" action="{{ route('device.uploadToEsp32', $device->id) }}">
        @csrf
        <button type="submit" class="btn btn-primary">Upload ke ESP32</button>
    </form>
@endsection
