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
            {{-- <div class="mb-3">
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
            </div> --}}

            <select name="ruangan_id" class="form-control" required id="ruangan_id">
                <option value="">Pilih Ruangan</option>
                @foreach ($ruanganTerhubung as $ruangan)
                    <option value="{{ $ruangan->id }}" data-group-index="{{ $ruangan->group_index }}">
                        {{ $ruangan->name }} (Grup {{ $ruangan->group_index }})
                    </option>
                @endforeach
            </select>



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
    {{-- <script>
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
    </script> --}}
    <script>
        document.getElementById('ruangan_id').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const groupIndex = selected.dataset.groupIndex;

            console.log('Group index:', groupIndex);
            // Bisa kamu pakai untuk isi field tersembunyi kalau perlu
            // document.getElementById('grup_id').value = groupIndex;
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#ruangan_id').select2({
                placeholder: "Cari ruangan...",
                allowClear: true
            });
        });
    </script>

@endsection
{{-- section('content')  --}}
