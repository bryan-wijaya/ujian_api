<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser
{

    protected function successResponse($data, $message = null, $code = 200)
    {
        $ret = [
            "status" => $code,
            "message" => $message,
            "data" => $data
        ];
        return response()->json($ret);
    }

    protected function errorResponse($message = null, $code = 404)
    {
        $ret = [
            "status" => $code,
            "message" => $message,
            "data" => null
        ];
        return response()->json($ret);
    }
}
