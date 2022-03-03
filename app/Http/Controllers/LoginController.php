<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    //

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|string|required_with:password',
            'password' => 'nullable|string|required_with:phone',
            'token' => 'nullable|string|required_with:svip',
            'svip' => 'nullable|string|required_with:token',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->all());
        }

        $url = 'https://jzy.bjyush.com';

        if ($request->filled('phone') && $request->filled('password')) {
            $response = Http::post($url, [
                'phone' => $request->phone,
                'password' => $request->password,
            ]);

            if ($response->status() == 200) {
                $data = $response->json();
                if ($data['code'] == 1) {
                    Cache::put('login', $data['data']);
                    return $this->success($data['data'], $data['message']);
                } else {
                    return $this->fail($data['message']);
                }
            } else {
                return $this->fail('登录失败');
            }

        }
    }
}
