<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\BoardOfDirector\BoardOfDirectorResource;
use App\Http\Resources\GeneralAssistant\GeneralAssistantResource;
use App\Http\Resources\Staff\StaffResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_id' => $this->id,
            'ga_approved_at' => $this->ga_approved_at,
            'bod_approved_at' => $this->bod_approved_at,
            'status' => $this->status,
            'rented_at' => $this->rented_at,
            'returned_at' => $this->returned_at,
            'board_of_director' => new BoardOfDirectorResource($this->boardOfDirector),
            'general_assistant' => new GeneralAssistantResource($this->generalAssistant),
            'user' => new UserResource($this->user),
            'vehicle' => $this->vehicle,
            'rent_image_before' => $this->rentImageBefore,
            'rent_image_after' => $this->rentImageAfter,
            'rent_reject' => $this->rentReject,
        ];
    }
}
