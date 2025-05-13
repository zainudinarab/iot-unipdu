@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Manajemen Kontrol Perangkat: {{ $device->name }}</h2>

        @foreach ($ruangans as $ruangan)
            <h5>{{ $ruangan->name }} (Group {{ $ruangan->pivot->group_index }})</h5>

            <!-- Tombol Tambah IR -->
            <a href="{{ route('device.control.create', ['device' => $device->id, 'ruangan' => $ruangan->id, 'type' => 'IR']) }}"
                class="btn btn-sm btn-primary">➕ Tambah IR</a>

            <!-- Tombol Tambah Relay -->
            <a href="{{ route('device.control.create', ['device' => $device->id, 'ruangan' => $ruangan->id, 'type' => 'RELAY']) }}"
                class="btn btn-sm btn-warning">➕ Tambah Relay</a>

            @php
                $controls = ($deviceControls[$ruangan->id] ?? collect())->sortBy(function ($control) {
                    return match ($control->type) {
                        'RELAY' => 0,
                        'IR' => 1,
                        'SENSOR' => 2,
                        default => 99,
                    };
                });
            @endphp

            @if ($controls->isNotEmpty())
                <table class="table table-sm table-bordered mt-2">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%">Type</th>
                            <th style="width: 10%">Chenel</th>
                            <th>Nama</th>
                            <th style="width: 30%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($controls as $control)
                            <tr>
                                <td>{{ $control->type }}</td>
                                <td>=>{{ $control->index }}</td>
                                <td>{{ $control->name }}</td>
                                <td>
                                    @if ($control->type === 'IR')
                                        <a href="{{ route('control-commands.index', ['device_control' => $control->id, 'type' => 'IR']) }}"
                                            class="btn btn-sm btn-success">➕ Tambah DataRow</a>
                                    @endif

                                    <form action="{{ route('device-controls.destroy', $control->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus kontrol ini?')">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">Belum ada kontrol untuk ruangan ini.</p>
            @endif



            <hr>
        @endforeach

    </div>
@endsection
