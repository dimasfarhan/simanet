<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleAccident;
use Illuminate\Support\Collection;

class VehicleService
{
    public function all($case = null): Collection
    {
        switch ($case) {
            case 'AVAILABLE':
                $vehicle = Vehicle::available()->orderBy('created_at', 'DESC')->get();
                break;
            case 'UNAVAILABLE':
                $vehicle = Vehicle::unavailable()->orderBy('created_at', 'DESC')->get();
                break;
            case 'RENTED':
                $vehicle = Vehicle::rented()->orderBy('created_at', 'DESC')->get();
                break;
            case 'MAINTENANCE':
                $vehicle = Vehicle::maintenance()->orderBy('created_at', 'DESC')->get();
                break;

            default:
                $vehicle = Vehicle::orderBy('created_at', 'DESC')->get();
                break;
        }

        return $vehicle;
    }

    public function available(): Collection
    {
        return Vehicle::available()->orderBy('created_at', 'DESC')->get();
    }

    public function get($id): Vehicle
    {
        return Vehicle::findOrFail($id);
    }

    public function store(array $data): Vehicle
    {
        $vehicle = Vehicle::create($data);

        if ($data['status'] == 'UNAVAILABLE' || $data['status'] == 'MAINTENANCE') {
            VehicleAccident::create([
                'vehicle_id' => $vehicle->id,
                'type' => $data['status'],
                'reason' => $data['reason']
            ]);
        }
        return $vehicle;
    }

    public function update(array $data, $id): Vehicle
    {
        $vehicle = $this->get($id);
        $vehicle->update($data);
        if ($data['status'] == 'UNAVAILABLE' || $data['status'] == 'MAINTENANCE') {
            $vehicleAccident = VehicleAccident::where('is_active', true)->first();
            $vehicleAccident->is_active = false;
            $vehicleAccident->save();
            VehicleAccident::create([
                'vehicle_id' => $vehicle->id,
                'type' => $data['status'],
                'reason' => $data['reason']
            ]);
        }
        return $vehicle;
    }

    public function delete($id): void
    {
        $vehicle = $this->get($id);
        if ($vehicle->status == 'UNAVAILABLE' || $vehicle->status == 'MAINTENANCE') {
            $vehicleAccident = VehicleAccident::where('is_active', true)->first();
            $vehicleAccident->is_active = false;
            $vehicleAccident->save();
        }
        $vehicle->delete();
    }
}
