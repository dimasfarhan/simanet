<?php

namespace App\Traits;

use stdClass;

trait ApiResponser
{
    /**
     * Build success response
     * @param  string|array $data
     * @param  int $code
     * @return Illuminate\Http\JsonResponse
     */
    public function baseResponse($code, $message = null, $data = new stdClass())
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'meta' => new \stdClass,
        ], $code);
    }
}
