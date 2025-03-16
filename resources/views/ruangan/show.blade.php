@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Detail Ruangan: {{ $ruangan->nama }}</h1>

        <div class="mb-3">
            <strong>ID:</strong> {{ $ruangan->id }}
        </div>

        <div class="mb-3">
            <strong>Nama Ruangan:</strong> {{ $ruangan->nama }}
        </div>

        <a href="{{ route('ruangan.edit', $ruangan->id) }}" class="btn btn-warning">Edit</a>

        <form action="{{ route('ruangan.destroy', $ruangan->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Hapus</button>
        </form>

        <h2 class="mt-4">Perangkat di Ruangan Ini</h2>
        <ul>
            @foreach ($ruangan->perangkat as $perangkat)
                <li>{{ $perangkat->nama }} - {{ $perangkat->topik_mqtt }}</li>
            @endforeach
        </ul>
    </div>
@endsection
