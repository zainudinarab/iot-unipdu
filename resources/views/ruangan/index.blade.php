@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Daftar Ruangan</h1>

        <a href="{{ route('ruangan.create') }}" class="btn btn-primary mb-3">Tambah Ruangan</a>

        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Ruangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ruangans as $ruangan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $ruangan->nama }}</td>
                        <td>
                            <a href="{{ route('ruangan.show', $ruangan->id) }}" class="btn btn-info btn-sm">Detail</a>
                            <a href="{{ route('ruangan.edit', $ruangan->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('ruangan.destroy', $ruangan->id) }}" method="POST" class="d-inline">
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
