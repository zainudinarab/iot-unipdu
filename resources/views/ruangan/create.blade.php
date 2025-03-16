@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Tambah Ruangan</h1>

        <form action="{{ route('ruangan.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="nama">Nama Ruangan</label>
                <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama') }}" required>
            </div>

            <button type="submit" class="btn btn-success mt-3">Simpan Ruangan</button>
        </form>
    </div>
@endsection
