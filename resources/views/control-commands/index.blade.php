@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Manajemen Perintah IR untuk: {{ $deviceControl->device->name }} — {{ $deviceControl->name }}</h2>
        <!-- Menampilkan pesan error jika ada -->
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Menampilkan pesan sukses jika ada -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        {{-- Tombol-tombol aksi --}}
        <div class="my-3">
            <a href="{{ route('control-commands.create', ['device_control' => $deviceControl->id]) }}"
                class="btn btn-success me-2">
                ➕ Tambah Perintah
            </a>

            <a href="{{ route('control-commands.update-code', ['device_control' => $deviceControl->id]) }}"
                class="btn btn-primary">
                🔄 Update Semua Kode
            </a>
        </div>

        {{-- Tabel DataRow --}}
        @if ($existingCommands->isEmpty())
            <div class="alert alert-warning">
                Belum ada data perintah IR yang ditambahkan.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 25%">Tipe Perintah</th>
                            <th>Data</th>
                            <th style="width: 20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($existingCommands as $i => $command)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $command->command_type }}</td>
                                <td><code>{{ Str::limit($command->data, 100) }}</code></td>
                                <td>
                                    <a href="{{ route('control-commands.edit', $command->id) }}"
                                        class="btn btn-warning">Edit</a>


                                    <form action="{{ route('control-commands.destroy', $command->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus perintah ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">🗑️ Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
