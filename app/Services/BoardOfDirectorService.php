<?php

namespace App\Services;

use App\Models\Role;
use App\Models\BoardOfDirector;
use App\Models\StaffDismissal;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class BoardOfDirectorService
{
    public function all(): Collection
    {
        return BoardOfDirector::active()->orderBy('created_at', 'DESC')->get();
    }

    public function get($uuid): BoardOfDirector | null
    {
        return BoardOfDirector::where('uuid', $uuid)->first();
    }

    public function store(array $data): BoardOfDirector
    {
        $role = Role::where('name', 'ga')->first();

        $user = User::create([
            'username' => $data['email'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $user->roles()->attach($role);
        $user->save();

        $bod = BoardOfDirector::create([
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

        return $bod;
    }

    public function update(array $data, $uuid): BoardOfDirector
    {
        $bod = $this->get($uuid);
        $bod->update([
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
            $bod->update(['is_active' => $data['is_active']]);
            if ($data['is_active'] === false) {
                StaffDismissal::create([
                    'staff_uuid' => $bod->uuid,
                    'dismissal_reason' => $data['dismissal_reason']
                ]);
            }
        }

        if (isset($data['date_joined'])) {
            $bod->update(['date_joined' => $data['date_joined']]);
        }

        $user = $bod->user;
        $user->update([
            'username' => $data['email'],
            'email' => $data['email'],
        ]);


        return $bod;
    }

    public function delete($uuid): void
    {
        $bod = $this->get($uuid);
        $user = $bod->user;

        $user->delete();
        $bod->delete();
    }

    public function resetPassword(array $data, $uuid): void
    {
        $bod = $this->get($uuid);
        $user = $bod->user;

        if (Hash::check($data['password'], $user->password)) {
            abort(400, 'Password value cannot be same as previous password!');
        }

        $user->update([
            'password' => bcrypt($data['password'])
        ]);
    }
}
