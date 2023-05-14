<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\CreateRequest;
use App\Http\Requests\Vehicle\UpdateRequest;
use App\Services\VehicleService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    use ApiResponser;

    protected $vehicleService;
    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    public function all(Request $request)
    {
        $data = $this->vehicleService->all($request->status);
        return $this->baseResponse(200, 'success', $data);
    }

    public function available(Request $request)
    {
        $data = $this->vehicleService->available($request->status);
        return $this->baseResponse(200, 'success', $data);
    }

    public function get($id)
    {
        $data = $this->vehicleService->get($id);
        return $this->baseResponse(200, 'success', $data);
    }

    public function store(CreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->vehicleService->store($request->validated());
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', ['msg' => $th->getMessage()]);
        }

        return $this->baseResponse(201, 'created', $data);
    }

    public function update(UpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $this->vehicleService->update($request->validated(), $id);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', ['msg' => $th->getMessage()]);
        }

        return $this->baseResponse(200, 'success', $data);
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $data = $this->vehicleService->delete($id);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', ['msg' => $th->getMessage()]);
        }

        return $this->baseResponse(200, 'deleted');
    }
}
