<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralAssistant\CreateRequest;
use App\Http\Requests\GeneralAssistant\ResetPasswordRequest;
use App\Http\Requests\GeneralAssistant\UpdateRequest;
use App\Http\Resources\GeneralAssistant\GeneralAssistantResource;
use App\Services\GeneralAssistantService;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;

class GeneralAssistantController extends Controller
{
    use ApiResponser;

    protected $gaService;
    public function __construct(GeneralAssistantService $gaService)
    {
        $this->gaService = $gaService;
    }

    public function all()
    {
        $result = $this->gaService->all();
        $results = GeneralAssistantResource::collection($result);
        return $this->baseResponse(200, 'Success', $results);
    }

    public function get($uuid)
    {
        try {
            $data = $this->gaService->get($uuid);
        } catch (\Throwable $th) {
            return $this->baseResponse(400, 'error', $th->getMessage());
        }
        $result = new GeneralAssistantResource($data);
        return $this->baseResponse(200, 'Success', $result);
    }

    public function store(CreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->gaService->store($request->validated());
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        $result = new GeneralAssistantResource($data);
        return $this->baseResponse(201, 'success', $result);
    }

    public function update(UpdateRequest $request, $uuid)
    {
        DB::beginTransaction();
        try {
            $data = $this->gaService->update($request->validated(), $uuid);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        $result = new GeneralAssistantResource($data);
        return $this->baseResponse(200, 'success', $result);
    }

    public function delete($uuid)
    {
        DB::beginTransaction();
        try {
            $this->gaService->delete($uuid);
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
            $this->gaService->resetPassword($request->validated(), $uuid);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        return $this->baseResponse(200, 'success');
    }
}
