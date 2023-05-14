<?php

namespace App\Services;

use App\Models\Role;
use App\Models\GeneralAssistant;
use App\Models\StaffDismissal;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class GeneralAssistantService
{
    public function all(): Collection
    {
        return GeneralAssistant::active()->orderBy('created_at', 'DESC')->get();
    }

    public function get($uuid): GeneralAssistant | null
    {
        return GeneralAssistant::where('uuid', $uuid)->first();
    }

    public function store(array $data): GeneralAssistant
    {
        $role = Role::where('name', 'ga')->first();

        $user = User::create([
            'username' => $data['email'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $user->roles()->attach($role);
        $user->save();

        $ga = GeneralAssistant::create([
            'user_uuid' => $user->uuid,
            'name' => $data['name'],
            'nip' => $data['nip'],
            'phone' => $data['phone'],
            'place_of_birth' => $data['place_of_birth'],
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'religion' => $data['religion'],
            'address' => $data['address'],
            'is_active' => true,
            'date_joined' => $data['date_joined'],
        ]);

        return $ga;
    }

    public function update(array $data, $uuid): GeneralAssistant
    {
        $ga = $this->get($uuid);
        $ga->update([
            'name' => $data['name'],
            'nip' => $data['nip'],
            'phone' => $data['phone'],
            'place_of_birth' => $data['place_of_birth'],
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'religion' => $data['religion'],
            'address' => $data['address']
        ]);

        if (isset($data['is_active'])) {
            $ga->update(['is_active' => $data['is_active']]);
            if ($data['is_active'] === false) {
                StaffDismissal::create([
                    'staff_uuid' => $ga->uuid,
                    'dismissal_reason' => $data['dismissal_reason']
                ]);
            }
        }

        if (isset($data['date_joined'])) {
            $ga->update(['date_joined' => $data['date_joined']]);
        }

        $user = $ga->user;
        $user->update([
            'username' => $data['email'],
            'email' => $data['email'],
        ]);


        return $ga;
    }

    public function delete($uuid): void
    {
        $ga = $this->get($uuid);
        $user = $ga->user;

        $user->delete();
        $ga->delete();
    }

    public function resetPassword(array $data, $uuid): void
    {
        $ga = $this->get($uuid);
        $user = $ga->user;

        if (Hash::check($data['password'], $user->password)) {
            abort(400, 'Password value cannot be same as previous password!');
        }

        $user->update([
            'password' => bcrypt($data['password'])
        ]);
    }
}
