<?php

namespace App\Http\Traits;

trait ApiJsonResponseTrait
{
    public function success($message, $code = 200)
    {
        return response()->json([
            'code'    => $code,
            'status'  => 'success',
            'message' => $message,
        ]);

    }

    public function failure($message, $code = 200)
    {
        return response()->json([
            'code'    => $code,
            'status'  => 'error',
            'message' => $message,
        ]);
    }

    public function responseWithData($message, $data = [], $code = 200)
    {
        return response()->json([
            'code'    => $code,
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ]);

    }
}
