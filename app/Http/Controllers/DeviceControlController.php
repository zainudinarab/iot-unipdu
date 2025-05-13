<?php

namespace App\Http\Controllers;

use App\Models\DeviceControl;
use App\Models\Device;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DeviceControlController extends Controller
{

    public function create(Device $device, Ruangan $ruangan, Request $request)
    {
        $type = $request->get('type'); // atau dari route param
        // dd($ruangan);
        // dd($ruangan);
        return view('device_controls.create', compact('device', 'ruangan', 'type'));
    }
    public function management(Device $device)
    {
        // Mengambil semua ruangan yang terhubung dengan device
        $ruangans = $device->ruangans;

        return view('device.management', compact('device', 'ruangans'));
    }

    public function store(Request $request, Device $device)
    {
        $validated = $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'type' => 'required|in:IR,RELAY,SENSOR',
            'name' => 'required|string|max:255',
        ]);

        // Ambil group_index dari pivot
        $groupIndex = $device->ruangans()
            ->where('ruangan_id', $validated['ruangan_id'])
            ->first()
            ->pivot
            ->group_index ?? null;

        if ($groupIndex === null) {
            return back()->withErrors(['ruangan_id' => 'Ruangan tidak terhubung ke device.']);
        }
        DeviceControl::create([
            'device_id'   => $device->id,
            'ruangan_id'  => $validated['ruangan_id'],
            'group_index' => $groupIndex,
            'type'        => $validated['type'],
            'name'        => $validated['name'],
            'index'       => 0, // sementara, akan di-reindex setelah ini
        ]);

        // Reindex ulang semua
        $this->reindexDeviceControls($device);


        return redirect()->route('device.management', $device->id)->with('success', 'Kontrol berhasil ditambahkan');
    }
    public function destroy(DeviceControl $deviceControl)
    {
        // Simpan device_id untuk digunakan dalam reindex
        $device = $deviceControl->device;

        // Hapus device control
        $deviceControl->delete();
        // Reindex ulang kontrol perangkat setelah penghapusan

        $this->reindexDeviceControls($device);
        // dd($device);
        return redirect()->route('device.management', $device->id)->with('success', 'Kontrol berhasil dihapus dan di-reindex');
    }


    private function reindexDeviceControls(Device $device)
    {
        $types = ['RELAY', 'IR', 'SENSOR'];

        foreach ($types as $type) {
            $index = 0;

            $controls = DeviceControl::where('device_id', $device->id)
                ->where('type', $type)
                ->orderBy('group_index')
                ->orderBy('id')
                ->get();

            foreach ($controls as $control) {
                $control->update(['index' => $index++]);
            }
        }
    }
}
