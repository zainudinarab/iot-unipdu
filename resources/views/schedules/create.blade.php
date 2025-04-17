@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Tambah Jadwal</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('schedules.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="device_id" class="form-label">Pilih Device</label>
                <select name="device_id" id="device_id" class="form-control" required>
                    <option value="">Pilih Device</option>
                    @foreach ($devices as $device)
                        <option value="{{ $device->id }}" data-ruangans='@json($device->ruangans)'>{{ $device->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="ruangan_id" class="form-label">Pilih Ruangan</label>
                <select name="ruangan_id" id="ruangan_id" class="form-control" required>
                    <option value="">Pilih Ruangan</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="relay_mask" class="form-label">Pilih Ruangan</label>
                <select name="relay_mask" class="form-control" required>
                    <option value="31">Ruangan 1 (0000011111) → Relay 1-5</option>
                    <option value="992">Ruangan 2 (1111100000) → Relay 6-10</option>
                </select>
                <small class="form-text text-muted">
                    Pilih relay sesuai ruangan: Ruangan 1 (Relay 1-5), Ruangan 2 (Relay 6-10).
                </small>
            </div>



            <div class="mb-3">
                <label for="on_time" class="form-label">Waktu ON</label>
                <input type="time" name="on_time" id="on_time" class="form-control">
            </div>

            <div class="mb-3">
                <label for="off_time" class="form-label">Waktu OFF</label>
                <input type="time" name="off_time" id="off_time" class="form-control">
            </div>
            <div class="mb-3">
                <label>Hari Aktif</label><br>
                @php
                    $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                @endphp
                @foreach ($dayNames as $index => $day)
                    <input type="checkbox" name="days[]" value="{{ $index }}"> {{ $day }}
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
        </form>
    </div>
    <script>
        document.getElementById('device_id').addEventListener('change', function() {
            let selectedOption = this.options[this.selectedIndex];
            let ruangans = JSON.parse(selectedOption.dataset.ruangans || '[]'); // Ambil ruangan dari data-ruangans
            let ruanganSelect = document.getElementById('ruangan_id');

            // Kosongkan dropdown ruangan
            ruanganSelect.innerHTML = '<option value="">Pilih Ruangan</option>';

            // Tambahkan ruangan sesuai dengan device yang dipilih
            ruangans.forEach(ruangan => {
                let option = new Option(ruangan.name, ruangan.id);
                ruanganSelect.appendChild(option);
            });
        });
    </script>
@endsection
{{-- section('content')  --}}
