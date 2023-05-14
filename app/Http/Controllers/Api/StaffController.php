<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\CreateRequest;
use App\Http\Requests\Staff\ResetPasswordRequest;
use App\Http\Requests\Staff\UpdateRequest;
use App\Http\Resources\Staff\StaffResource;
use App\Services\StaffService;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    use ApiResponser;

    protected $staffService;
    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    public function all()
    {
        $result = $this->staffService->all();
        $results = StaffResource::collection($result);
        return $this->baseResponse(200, 'Success', $results);
    }

    public function get($uuid)
    {
        try {
            $data = $this->staffService->get($uuid);
        } catch (\Throwable $th) {
            return $this->baseResponse(400, 'error', $th->getMessage());
        }
        $result = new StaffResource($data);
        return $this->baseResponse(200, 'Success', $result);
    }

    public function store(CreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->staffService->store($request->validated());
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        $result = new StaffResource($data);
        return $this->baseResponse(201, 'success', $result);
    }

    public function update(UpdateRequest $request, $uuid)
    {
        DB::beginTransaction();
        try {
            $data = $this->staffService->update($request->validated(), $uuid);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        $result = new StaffResource($data);
        return $this->baseResponse(200, 'success', $result);
    }

    public function delete($uuid)
    {
        DB::beginTransaction();
        try {
            $result = $this->staffService->delete($uuid);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        return $this->baseResponse(200, 'deleted');
    }

    public function resetPassword(ResetPasswordRequest $request, $uuid)
    {
        DB::beginTransaction();
        try {
            $this->staffService->resetPassword($request->validated(), $uuid);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        return $this->baseResponse(200, 'success');
    }
}
