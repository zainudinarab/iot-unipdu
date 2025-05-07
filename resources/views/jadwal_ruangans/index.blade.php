@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Daftar Jadwal Ruangan</h3>

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
        @endif

        {{-- Tombol Import JSON --}}
        <a href="{{ route('jadwal.import') }}" class="btn btn-primary">
            Import Jadwal dari JSON
        </a>

        {{-- Tombol Tambah Manual (opsional) --}}

        <a href="{{ route('jadwal-ruangans.create') }}" class="btn btn-primary">
            Tambah Jadwal
        </a>
        {{-- Tabel Jadwal --}}
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ruangan</th>
                    <th>Hari (0–6)</th>
                    <th>Jam ON</th>
                    <th>Jam OFF</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwals as $jadwal)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $jadwal->ruangan }}</td>
                        <td>{{ $jadwal->days }}</td>
                        <td>{{ $jadwal->on_time }}</td>
                        <td>{{ $jadwal->off_time }}</td>
                        <td>
                            <a href="{{ route('jadwal-ruangans.edit', $jadwal->id) }}" class="btn btn-warning btn-sm">✏️
                                Edit</a>

                            <form action="{{ route('jadwal-ruangans.destroy', $jadwal->id) }}" method="POST"
                                class="d-inline-block" onsubmit="return confirm('Hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">🗑️ Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada jadwal ruangan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
