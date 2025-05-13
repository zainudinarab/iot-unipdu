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
                    @php
                        // Kelompokkan berdasarkan group_index
                        $groupData = $device->ruangans->groupBy('pivot.group_index')->map(function ($groupedRuangans) {
                            // Ambil status ON/OFF — misalnya status salah satu ruangan di grup
                            return $groupedRuangans->first()->pivot->status;
                        });
                    @endphp
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
                            {{-- @if (!$device->sys)
                                <button class="btn btn-warning syncSchedules" data-device-id="{{ $device->id }}">
                                    Sinkronkan
                                </button>
                            @else
                                <span class="badge bg-success">Sudah Sinkron</span>
                            @endif --}}

                            <a href="{{ route('device.jadwalUpload', $device->id) }}" class="btn btn-warning">
                                Upload Jadwal
                            </a>

                            <!-- Tombol untuk menambahkan IR di device -->
                            <a href="{{ route('device.ir.create', ['deviceId' => $device->id, 'acIndex' => 0]) }}"
                                class="btn btn-primary btn-sm">Tambah IR (AC 1)</a>
                            <a href="{{ route('device.ir.create', ['deviceId' => $device->id, 'acIndex' => 1]) }}"
                                class="btn btn-primary btn-sm">Tambah IR (AC 2)</a>
                            <a href="{{ route('device.ir.create', ['deviceId' => $device->id, 'acIndex' => 2]) }}"
                                class="btn btn-primary btn-sm">Tambah IR (AC 3)</a>
                            <a href="{{ route('device.ir.create', ['deviceId' => $device->id, 'acIndex' => 3]) }}"
                                class="btn btn-primary btn-sm">Tambah IR (AC 4)</a>
                            @foreach ($groupData as $groupIndex => $status)
                                @php
                                    // Ambil nama ruangan berdasarkan group_index
                                    $ruangan = $device->ruangans->firstWhere('pivot.group_index', $groupIndex);
                                    $ruanganName = $ruangan ? $ruangan->name : 'Unknown'; // Jika tidak ada, tampilkan 'Unknown'
                                @endphp
                                <div class="form-check form-switch">
                                    <!-- Toggle switch -->
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="statusSwitch{{ $groupIndex }}" data-grup-id="{{ $groupIndex }}"
                                        {{ $status ? 'checked' : '' }}>

                                    <label class="form-check-label" for="statusSwitch{{ $groupIndex }}">Kelas
                                        : {{ $ruanganName }} ({{ $groupIndex }})</label>
                                </div>
                            @endforeach
                            <a href="{{ route('device.management', ['device' => $device->id]) }}"
                                class="btn btn-primary mb-3">
                                Manajemen Kontrol Perangkat
                            </a>


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
        document.querySelectorAll('.form-check-input').forEach(switchElement => {
            switchElement.addEventListener('change', function() {
                const grupID = this.dataset.grupId; // Ambil ID grup
                const action = this.checked ? 'ON' :
                    'OFF'; // Tentukan status berdasarkan apakah switch dicentang

                fetch(`/control/grup/${grupID}/${action}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', // CSRF token untuk keamanan
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: action
                        }) // Kirim data action (ON/OFF)
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Tampilkan notifikasi sukses jika diperlukan
                        alert(`Group G${grupID} berhasil di-${action}`);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat mengubah status');
                        // Kembalikan toggle switch ke status semula jika terjadi error
                        this.checked = !this.checked;
                    });
            });
        });
    </script>
@endsection
