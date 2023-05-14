<?php

namespace App\Http\Resources\Profile;

use App\Http\Resources\BoardOfDirector\BoardOfDirectorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->user;
        return [
            'uuid' => $user->uuid,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->roles()->first()->name,
            'child' => [
                "id" => $this->id,
                "user_uuid" => $this->user_uuid,
                "uuid" => $this->uuid,
                "name" => $this->name,
                "nip" => $this->nip,
                "phone" => $this->phone,
                "place_of_birth" => $this->place_of_birth,
                "date_of_birth" => $this->date_of_birth,
                "gender" => $this->gender,
                "religion" => $this->religion,
                "address" => $this->address,
                "is_active" => $this->is_active,
                "date_joined" => $this->date_joined,
            ]
        ];
    }
}
