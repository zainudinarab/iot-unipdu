@extends('layouts.app')

@section('content')
    <div class="container">
        <h4>Tambah Kontrol Baru ({{ $type }}) untuk {{ $device->name }}</h4>

        <form action="{{ route('device.control.store', $device->id) }}" method="POST">
            @csrf

            <input type="hidden" name="type" value="{{ $type }}">

            <!-- Tampilkan nama ruangan -->
            <div class="mb-3">
                <label class="form-label">Ruangan</label>
                <p><strong>{{ $ruangan->name }}</strong></p>
                <input type="hidden" name="ruangan_id" value="{{ $ruangan->id }}">
            </div>


            <div class="mb-3">
                <label for="name" class="form-label">Nama Kontrol</label>
                <input type="text" name="name" class="form-control" required>
            </div>



            <button type="submit" class="btn btn-primary">Simpan Kontrol</button>
            {{-- <a href="{{ route('device.show', $device->id) }}" class="btn btn-secondary">Batal</a> --}}
        </form>
    </div>
@endsection
