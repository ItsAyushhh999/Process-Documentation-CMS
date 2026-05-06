<?php

namespace App\Http\Traits;

trait ApiTrait
{
    public function success($message, $data = [], $code = 200)
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'   => $data,
        ], $code);
    }

    public function failure($message, $code = 200)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }
}
