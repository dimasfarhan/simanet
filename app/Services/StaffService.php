<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Staff;
use App\Models\StaffDismissal;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class StaffService
{
    public function all(): Collection
    {
        return Staff::active()->orderBy('created_at', 'DESC')->get();
    }

    public function get($uuid): Staff | null
    {
        return Staff::where('uuid', $uuid)->first();
    }

    public function store(array $data): Staff
    {
        $role = Role::where('name', 'staff')->first();

        $user = User::create([
            'username' => $data['email'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $user->roles()->attach($role);
        $user->save();

        $staff = Staff::create([
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

        return $staff;
    }

    public function update(array $data, $uuid): Staff
    {
        $staff = $this->get($uuid);
        $staff->update([
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
            $staff->update(['is_active' => $data['is_active']]);
            if ($data['is_active'] === false) {
                StaffDismissal::create([
                    'staff_uuid' => $staff->uuid,
                    'dismissal_reason' => $data['dismissal_reason']
                ]);
            }
        }

        if (isset($data['date_joined'])) {
            $staff->update(['date_joined' => $data['date_joined']]);
        }

        $user = $staff->user;
        $user->update([
            'username' => $data['email'],
            'email' => $data['email'],
        ]);


        return $staff;
    }

    public function delete($uuid): void
    {
        $staff = $this->get($uuid);
        $user = $staff->user;

        $user->delete();
        $staff->delete();
    }

    public function resetPassword(array $data, $uuid): void
    {
        $staff = $this->get($uuid);
        $user = $staff->user;

        if (Hash::check($data['password'], $user->password)) {
            abort(400, 'Password value cannot be same as previous password!');
        }

        $user->update([
            'password' => bcrypt($data['password'])
        ]);
    }
}
