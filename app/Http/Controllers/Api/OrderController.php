<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateRentRequest;
use App\Http\Requests\Order\DoneRentRequest;
use App\Http\Requests\Order\RejectRentRequest;
use App\Http\Requests\Order\UpdateRentRequest;
use App\Http\Resources\Order\OrderResource;
use App\Services\OrderService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ApiResponser;

    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function all()
    {
        $data = $this->orderService->all();
        $result = OrderResource::collection($data);
        return $this->baseResponse(200, 'success', $result);
    }

    public function get($id)
    {
        $data = $this->orderService->get($id);
        $result = new OrderResource($data);
        return $this->baseResponse(200, 'success', $result);
    }

    public function createOrder(CreateRentRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $this->orderService->requestOrder($request->validated());
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        $result = new OrderResource($data);
        return $this->baseResponse(200, 'success', $result);
    }

    public function updateOrder(UpdateRentRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $this->orderService->updateRequestOrder($request->validated(), $id);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        $result = new OrderResource($data);
        return $this->baseResponse(200, 'success', $result);
    }

    public function submitOrder($id)
    {
        DB::beginTransaction();
        try {
            $data = $this->orderService->submitOrder($id);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        $result = new OrderResource($data);
        return $this->baseResponse(200, 'success', $result);
    }

    public function approvedByGa($id)
    {
        DB::beginTransaction();
        try {
            $data = $this->orderService->gaApproved($id);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        $result = new OrderResource($data);
        return $this->baseResponse(200, 'success', $result);
    }

    public function approvedByBod($id)
    {
        DB::beginTransaction();
        try {
            $data = $this->orderService->bodApproved($id);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        $result = new OrderResource($data);
        return $this->baseResponse(200, 'success', $result);
    }

    public function completeOrder(DoneRentRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $this->orderService->orderDone($request->validated(), $id);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        $result = new OrderResource($data);
        return $this->baseResponse(200, 'success', $result);
    }

    public function rejectOrder(RejectRentRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $this->orderService->orderReject($request->validated(), $id);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->baseResponse(400, 'error', $th->getMessage());
        }

        $result = new OrderResource($data);
        return $this->baseResponse(200, 'success', $result);
    }
}
