@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Daftar Device</h2>
        <a href="{{ route('devices.create') }}" class="btn btn-success mb-3">Tambah Device</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>MAC Address</th>
                    <th>MQTT Topic</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($devices as $index => $device)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $device->name }}</td>
                        <td>{{ $device->mac_address }}</td>
                        <td>{{ $device->mqtt_topic }}</td>
                        <td>
                            <a href="{{ route('devices.edit', $device->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('devices.destroy', $device->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus device ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>

                            @if ($device->sys)
                                <button class="btn btn-warning syncSchedules" data-device-id="{{ $device->id }}">
                                    Sinkronkan
                                </button>
                            @else
                                <span class="badge bg-success">Sudah Sinkron</span>
                            @endif
                            <button class="btn btn-success manual-control" data-action="ON" data-grup-id="0">
                                on G0
                            </button>

                            <button class="btn btn-danger manual-control" data-action="OFF" data-grup-id="0">
                                off G0
                            </button>

                            <button class="btn btn-success manual-control" data-action="ON" data-grup-id="1">
                                on G1
                            </button>

                            <button class="btn btn-danger manual-control" data-action="OFF" data-grup-id="1">
                                Off G1
                            </button>

                            <button class="btn btn-success manual-control" data-action="ON" data-grup-id="2">
                                On G2
                            </button>

                            <button class="btn btn-danger manual-control" data-action="OFF" data-grup-id="2">
                                Off G2
                            </button>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        document.querySelectorAll('.syncSchedules').forEach(button => {
            button.addEventListener('click', function() {
                let deviceId = this.getAttribute('data-device-id');

                if (!confirm("Apakah Anda yakin ingin menyinkronkan jadwal ke device?")) {
                    return;
                }

                fetch(`/devices/sync/${deviceId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Response dari server:", data); // Debugging log
                        if (data.status === 'success') {
                            alert(data.message);
                            // Sembunyikan tombol setelah sukses
                            document.querySelector(`.syncSchedules[data-device-id="${deviceId}"]`)
                                .outerHTML = '<span class="badge bg-success">Sudah Sinkron</span>';
                        } else {
                            alert("Gagal menyinkronkan jadwal!");
                        }
                    })
                    .catch(error => alert("Terjadi kesalahan!"));
            });
        });
    </script>
    <script>
        document.querySelectorAll('.manual-control').forEach(button => {
            button.addEventListener('click', function() {
                let action = this.getAttribute('data-action'); // ON atau OFF
                let grupID = this.getAttribute('data-grup-id'); // ID grup (0, 1, atau 2)

                if (!confirm(
                        `Apakah Anda yakin ingin ${action === 'ON' ? 'menghidupkan' : 'mematikan'} Grup ${grupID}?`
                    )) {
                    return;
                }

                // Kirim request POST ke controller
                fetch(`/control/grup/${grupID}/${action}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', // CSRF token untuk keamanan
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: action
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Response dari server:", data); // Debugging log
                        if (data.success) {
                            alert(
                                `Grup ${grupID} berhasil ${action === 'ON' ? 'dihidupkan' : 'dimatikan'}.`
                            );
                        } else {
                            alert('Gagal mengirim perintah.');
                        }
                    })
                    .catch(error => alert('Terjadi kesalahan!'));
            });
        });
    </script>
@endsection
