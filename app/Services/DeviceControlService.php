<?php

namespace App\Services;

use App\Models\DeviceControl;
use App\Models\Device;

class DeviceControlService
{
    public function syncDeviceControlsIndex(Device $device, array $ruanganWithGroup)
    {
        $index = 0;

        // Urutkan berdasarkan group_index
        $sorted = collect($ruanganWithGroup)->sortBy('group_index');

        foreach ($sorted as $ruanganId => $groupData) {
            $groupIndex = $groupData['group_index'];

            foreach (['RELAY', 'IR', 'SENSOR'] as $type) {
                $controls = DeviceControl::where('ruangan_id', $ruanganId)
                    ->where('type', $type)
                    ->orderBy('id')
                    ->get();

                foreach ($controls as $control) {
                    $control->update([
                        'index' => $index++,
                        'group_index' => $groupIndex,
                        'device_id' => $device->id, // ✅ update device_id juga
                    ]);
                }
            }
        }
    }
}
