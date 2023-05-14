<?php

namespace App\Http\Resources\BoardOfDirector;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardOfDirectorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
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
            "user" => new UserResource($this->user)
        ];
    }
}
