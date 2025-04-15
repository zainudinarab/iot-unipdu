@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Daftar Jadwal</h2>
        <a href="{{ route('schedules.create') }}" class="btn btn-success mb-3">Tambah Jadwal</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Device</th>
                    <th>Ruangan</th>
                    <th>Relay (Bin)</th>
                    <th>Grup</th>
                    <th>Waktu ON</th>
                    <th>Waktu OFF</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($schedules as $index => $schedule)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $schedule->device->name }}</td>
                        <td>{{ $schedule->ruangan->name }}</td>
                        <td>{{ sprintf('%08b', $schedule->relay_mask) }}</td>
                        <td>{{ $schedule->grup_id }}</td>
                        <td>{{ $schedule->on_time }}</td>
                        <td>{{ $schedule->off_time }}</td>
                        <td>
                            <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus jadwal ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
