@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Jadwal</h2>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('schedules.update', $schedule->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="device_id" class="form-label">Pilih Device</label>
                <select name="device_id" id="device_id" class="form-control" required>
                    <option value="">Pilih Device</option>
                    @foreach ($devices as $device)
                        <option value="{{ $device->id }}" data-ruangans='@json($device->ruangans)'
                            {{ $schedule->device_id == $device->id ? 'selected' : '' }}>
                            {{ $device->name }}
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
                    <option value="31" {{ $schedule->relay_mask == 31 ? 'selected' : '' }}>Ruangan 1 (00011111) → Relay
                        1-5</option>
                    <option value="992" {{ $schedule->relay_mask == 992 ? 'selected' : '' }}>Ruangan 2 (1111100000) →
                        Relay 6-10</option>
                </select>
            </div>


            <div class="mb-3">
                <label for="on_time" class="form-label">Waktu ON</label>
                <input type="time" name="on_time" id="on_time" class="form-control" value="{{ $schedule->on_time }}">
            </div>

            <div class="mb-3">
                <label for="off_time" class="form-label">Waktu OFF</label>
                <input type="time" name="off_time" id="off_time" class="form-control" value="{{ $schedule->off_time }}">
            </div>
            <div class="mb-3">
                <label>Hari Aktif</label><br>
                @php
                    $selectedDays = $schedule->days; // Ambil nilai dari model
                @endphp
                <input type="checkbox" name="days[]" value="0" {{ in_array(0, $selectedDays) ? 'checked' : '' }}>
                Minggu
                <input type="checkbox" name="days[]" value="1" {{ in_array(1, $selectedDays) ? 'checked' : '' }}>
                Senin
                <input type="checkbox" name="days[]" value="2" {{ in_array(2, $selectedDays) ? 'checked' : '' }}>
                Selasa
                <input type="checkbox" name="days[]" value="3" {{ in_array(3, $selectedDays) ? 'checked' : '' }}>
                Rabu
                <input type="checkbox" name="days[]" value="4" {{ in_array(4, $selectedDays) ? 'checked' : '' }}>
                Kamis
                <input type="checkbox" name="days[]" value="5" {{ in_array(5, $selectedDays) ? 'checked' : '' }}>
                Jumat
                <input type="checkbox" name="days[]" value="6" {{ in_array(6, $selectedDays) ? 'checked' : '' }}>
                Sabtu
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('schedules.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
    <script>
        function updateRuanganDropdown(selectedDeviceId, selectedRuanganId = null) {
            let deviceSelect = document.getElementById('device_id');
            let selectedOption = deviceSelect.options[deviceSelect.selectedIndex];
            let ruangans = JSON.parse(selectedOption.dataset.ruangans || '[]');
            let ruanganSelect = document.getElementById('ruangan_id');

            ruanganSelect.innerHTML = '<option value="">Pilih Ruangan</option>';

            ruangans.forEach(ruangan => {
                let option = new Option(ruangan.name, ruangan.id);
                if (selectedRuanganId && ruangan.id == selectedRuanganId) {
                    option.selected = true;
                }
                ruanganSelect.appendChild(option);
            });
        }

        document.getElementById('device_id').addEventListener('change', function() {
            updateRuanganDropdown(this.value);
        });

        // Saat halaman dimuat, jalankan fungsi untuk menampilkan ruangan yang sesuai
        document.addEventListener('DOMContentLoaded', function() {
            let selectedDeviceId = document.getElementById('device_id').value;
            let selectedRuanganId = "{{ $schedule->ruangan_id }}"; // Ruangan yang sudah tersimpan
            updateRuanganDropdown(selectedDeviceId, selectedRuanganId);
        });
    </script>
@endsection
