<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $url = 'https://jzy.bjyush.com';

    public function fail($msg)
    {
        return response()->json([
            'code' => 0,
            'status' => 'fail',
            'msg' => $msg,
        ]);
    }

    public function success($data, $msg)
    {
        return response()->json([
            'code' => 1,
            'status' => 'success',
            'msg' => $msg,
            'data' => $data,
        ]);
    }
}
