<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BoardOfDirector\CreateRequest;
use App\Http\Requests\BoardOfDirector\ResetPasswordRequest;
use App\Http\Requests\BoardOfDirector\UpdateRequest;
use App\Http\Resources\BoardOfDirector\BoardOfDirectorResource;
use App\Services\BoardOfDirectorService;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\DB;

class BoardOfDirectorController extends Controller
{
    use ApiResponser;

    protected $bodService;
    public function __construct(BoardOfDirectorService $bodService)
    {
        $this->bodService = $bodService;
    }

    public function all()
    {
        $result = $this->bodService->all();
        $results = BoardOfDirectorResource::collection($result);
        return $this->baseResponse(200, 'Success', $results);
    }

    public function get($uuid)
    {
        try {
            $data = $this->bodService->get($uuid);
        } catch (\Throwable $th) {
            return $this->baseResponse(400, 'error', $th->getMessage());
        }
        $result = new BoardOfDirectorResource($data);
        return $this->baseResponse(200, 'Success', $result);
    }

    public function store(CreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->bodService->store($request->validated());
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        $result = new BoardOfDirectorResource($data);
        return $this->baseResponse(201, 'success', $result);
    }

    public function update(UpdateRequest $request, $uuid)
    {
        DB::beginTransaction();
        try {
            $data = $this->bodService->update($request->validated(), $uuid);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        $result = new BoardOfDirectorResource($data);
        return $this->baseResponse(200, 'success', $result);
    }

    public function delete($uuid)
    {
        DB::beginTransaction();
        try {
            $this->bodService->delete($uuid);
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
            $this->bodService->resetPassword($request->validated(), $uuid);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        return $this->baseResponse(200, 'success');
    }
}
