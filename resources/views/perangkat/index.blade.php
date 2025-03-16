@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Daftar Perangkat</h1>

        <a href="{{ route('perangkat.create') }}" class="btn btn-primary mb-3">Tambah Perangkat</a>

        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Perangkat</th>
                    <th>Topik MQTT</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($perangkat as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->topik_mqtt }}</td>
                        <td>
                            <a href="{{ route('perangkat.show', $item->id) }}" class="btn btn-info btn-sm">Detail</a>
                            <a href="{{ route('perangkat.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('perangkat.destroy', $item->id) }}" method="POST" class="d-inline">
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
