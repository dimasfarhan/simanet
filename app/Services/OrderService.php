<?php

namespace App\Services;

use App\Models\RentImageAfter;
use App\Models\RentImageBefore;
use App\Models\RentOrder;
use App\Models\RentOrderReject;
use App\Models\Vehicle;
use Carbon\Carbon;

class OrderService
{
    public function all()
    {
        return RentOrder::orderBy('created_at', 'DESC')->get();
    }

    public function get($order_id)
    {
        return RentOrder::findOrFail($order_id);
    }

    public function requestOrder(array $data): RentOrder
    {
        $user = auth()->user();
        $startDate = Carbon::createFromFormat('Y-m-d', $data['rented_at']);
        $endDate = Carbon::createFromFormat('Y-m-d', $data['returned_at']);

        /**
         * Cari data RentOrder dengan tanggal
         * yang overlap dengan tanggal yg
         * diberikan
         */
        $bookingData = RentOrder::where('vehicle_id', $data['vehicle_id'])
            ->whereNotIn('status', [
                RentOrder::STATUS_SUBMITTED,
                RentOrder::STATUS_REJECT,
                RentOrder::STATUS_DONE
            ])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('rented_at', [$startDate, $endDate])
                    ->orWhereBetween('returned_at', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('rented_at', '<', $startDate)
                            ->where('returned_at', '>', $endDate);
                    });
            })->count();

        if ($bookingData > 0) {
            abort(400, 'This vehicle is booked on given date!');
        }

        $checkOrder = RentOrder::where('user_uuid', $user->uuid)
            ->whereNotIn('status', [
                RentOrder::STATUS_REJECT,
                RentOrder::STATUS_DONE
            ])
            ->count();

        if ($checkOrder > 0) {
            abort(400, 'Please complete your previous order');
        }

        $vehicle = Vehicle::findOrFail($data['vehicle_id']);
        if ($vehicle->status != 'AVAILABLE') {
            abort(400, 'This vehicle is not available!');
        }

        $order = RentOrder::create([
            'vehicle_id' => $data['vehicle_id'],
            'user_uuid' => $user->uuid,
            'rented_at' => $data['rented_at'],
            'returned_at' => $data['returned_at'],
            'status' => RentOrder::STATUS_SUBMITTED
        ]);

        $photo_rear = upload($data['photo_rear'], 'images/rentorder/' . $order->id . '/before');
        $photo_front = upload($data['photo_front'], 'images/rentorder/' . $order->id . '/before');
        $photo_side = upload($data['photo_side'], 'images/rentorder/' . $order->id . '/before');

        RentImageBefore::create([
            'rent_order_id' => $order->id,
            'photo_rear' => $photo_rear,
            'photo_front' => $photo_front,
            'photo_side' => $photo_side,
        ]);

        return $order;
    }

    public function updateRequestOrder(array $data, $id): RentOrder
    {
        $user = auth()->user();
        $order = $this->get($id);
        $startDate = Carbon::createFromFormat('Y-m-d', $data['rented_at']);
        $endDate = Carbon::createFromFormat('Y-m-d', $data['returned_at']);

        if ($order->status !== RentOrder::STATUS_SUBMITTED) {
            abort(400, 'This order is already procces. Cannot update again!');
        }

        /**
         * Cari data RentOrder dengan tanggal
         * yang overlap dengan tanggal yg
         * diberikan
         */
        $bookingData = RentOrder::where('vehicle_id', $data['vehicle_id'])
            ->whereNotIn('status', [
                RentOrder::STATUS_SUBMITTED,
                RentOrder::STATUS_REJECT,
                RentOrder::STATUS_DONE
            ])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('rented_at', [$startDate, $endDate])
                    ->orWhereBetween('returned_at', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('rented_at', '<', $startDate)
                            ->where('returned_at', '>', $endDate);
                    });
            })->count();

        if ($bookingData > 0) {
            abort(400, 'This vehicle is booked on given date!');
        }

        $vehicle = Vehicle::findOrFail($data['vehicle_id']);
        if ($vehicle->status != 'AVAILABLE') {
            abort(400, 'This vehicle is not available!');
        }

        $order->update([
            'vehicle_id' => $data['vehicle_id'],
            'user_uuid' => $user->uuid,
            'rented_at' => $data['rented_at'],
            'returned_at' => $data['returned_at']
        ]);

        $imageBefore = $order->rentImageBefore;

        if (isset($data['photo_rear'])) {
            $photo_rear = upload(
                $data['photo_rear'],
                'images/rentorder/' . $order->id . '/before',
                null,
                @$imageBefore->photo_rear
            );
        } else {
            unset($data['photo_rear']);
            $photo_rear = $imageBefore->photo_rear;
        }

        if (isset($data['photo_front'])) {
            $photo_front = upload(
                $data['photo_front'],
                'images/rentorder/' . $order->id . '/before',
                null,
                @$imageBefore->photo_front
            );
        } else {
            unset($data['photo_front']);
            $photo_front = $imageBefore->photo_front;
        }

        if (isset($data['photo_side'])) {
            $photo_side = upload(
                $data['photo_side'],
                'images/rentorder/' . $order->id . '/before',
                null,
                @$imageBefore->photo_side
            );
        } else {
            unset($data['photo_side']);
            $photo_side = $imageBefore->photo_side;
        }

        $imageBefore->update([
            'rent_order_id' => $order->id,
            'photo_rear' => $photo_rear,
            'photo_front' => $photo_front,
            'photo_side' => $photo_side,
        ]);

        return $order;
    }

    public function submitOrder($order_id)
    {
        $order = $this->get($order_id);
        $bookingData = RentOrder::where('vehicle_id', $order->vehicle_id)
            ->whereNotIn('status', [
                RentOrder::STATUS_SUBMITTED,
                RentOrder::STATUS_REJECT,
                RentOrder::STATUS_DONE
            ])
            ->where(function ($query) use ($order) {
                $query->whereBetween('rented_at', [$order->rented_at, $order->returned_at])
                    ->orWhereBetween('returned_at', [$order->rented_at, $order->returned_at])
                    ->orWhere(function ($query) use ($order) {
                        $query->where('rented_at', '<', $order->rented_at)
                            ->where('returned_at', '>', $order->returned_at);
                    });
            })->count();

        if ($bookingData > 0) {
            abort(400, 'This vehicle is already booked on given date. Please change your booking date!');
        }

        $vehicle = Vehicle::findOrFail($order->vehicle_id);
        if ($vehicle->status != 'AVAILABLE') {
            abort(400, 'This vehicle is not available!');
        }

        $order->status = RentOrder::STATUS_WAITING_FOR_APPROVAL;
        $order->save();

        return $order;
    }

    public function gaApproved($order_id)
    {
        $user = auth()->user();
        $order = $this->get($order_id);

        if ($order->status != RentOrder::STATUS_WAITING_FOR_APPROVAL) {
            abort(400, 'Cannot approve this order because status order is ' . $order->status);
        }

        $order->status = RentOrder::STATUS_APPROVED_BY_GA;
        $order->ga_uuid = $user->child->uuid;
        $order->ga_approved_at = Carbon::now();
        $order->save();

        return $order;
    }

    public function bodApproved($order_id)
    {
        $user = auth()->user();
        $order = $this->get($order_id);

        if ($order->status != RentOrder::STATUS_APPROVED_BY_GA) {
            abort(400, 'Cannot approve this order because status order is ' . $order->status);
        }

        $order->status = RentOrder::STATUS_APPROVED;
        $order->ga_uuid = $user->child->uuid;
        $order->ga_approved_at = Carbon::now();
        $order->save();

        return $order;
    }

    public function orderDone(array $data, $order_id)
    {
        $user = auth()->user();
        $order = $this->get($order_id);

        if ($order->status != RentOrder::STATUS_APPROVED) {
            abort(400, 'Cannot approve this order because status order is ' . $order->status);
        }

        $order->status = RentOrder::STATUS_DONE;
        $order->ga_uuid = $user->child->uuid;
        $order->ga_approved_at = Carbon::now();
        $order->save();

        $photo_rear = upload($data['photo_rear'], 'images/rentorder/' . $order->id . '/after');
        $photo_front = upload($data['photo_front'], 'images/rentorder/' . $order->id . '/after');
        $photo_side = upload($data['photo_side'], 'images/rentorder/' . $order->id . '/after');

        RentImageAfter::create([
            'rent_order_id' => $order->id,
            'photo_rear' => $photo_rear,
            'photo_front' => $photo_front,
            'photo_side' => $photo_side,
        ]);

        return $order;
    }

    public function orderReject(array $data, $order_id)
    {
        $user = auth()->user();
        $order = $this->get($order_id);

        if (
            $order->status != RentOrder::STATUS_WAITING_FOR_APPROVAL &&
            $order->status != RentOrder::STATUS_APPROVED_BY_GA
        ) {
            abort(400, 'Cannot approve this order because status order is ' . $order->status);
        }

        $order->status = RentOrder::STATUS_REJECT;
        $order->save();

        RentOrderReject::create([
            'rent_order_id' => $order->id,
            'rejected_by' => $user->uuid,
            'reason' => $data['reason']
        ]);

        return $order;
    }
}
