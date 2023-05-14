<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateRequest;
use App\Http\Requests\Staff\ResetPasswordRequest;
use App\Http\Resources\Profile\UserResource;
use App\Services\BoardOfDirectorService;
use App\Services\GeneralAssistantService;
use App\Services\StaffService;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    use ApiResponser;

    protected $staffService;
    protected $gaService;
    protected $bodService;
    public function __construct(
        StaffService $staffService,
        GeneralAssistantService $gaService,
        BoardOfDirectorService $bodService
    ) {
        $this->staffService = $staffService;
        $this->gaService = $gaService;
        $this->bodService = $bodService;
    }

    protected function chooseService()
    {
        $user = auth()->user();
        $child = $user->child;
        $service = null;
        switch ($user->roles()->first()->name) {
            case 'bod':
                $service = $this->bodService;
                break;

            case 'ga':
                $service = $this->gaService;
                break;

            case 'staff':
                $service = $this->staffService;
                break;

            default:
                $service = $this->staffService;
                break;
        }

        return [
            'uuid' => $child->uuid,
            'service' => $service
        ];
    }

    public function get()
    {
        $self = $this->chooseService();
        try {
            $data = $self['service']->get($self['uuid']);
        } catch (\Throwable $th) {
            return $this->baseResponse(400, 'error', $th->getMessage());
        }
        $result = new UserResource($data);
        return $this->baseResponse(200, 'Success', $result);
    }

    public function update(UpdateRequest $request)
    {
        $self = $this->chooseService();
        DB::beginTransaction();
        try {
            $data = $self['service']->update($request->validated(), $self['uuid']);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        $result = new UserResource($data);
        return $this->baseResponse(200, 'success', $result);
    }
}
