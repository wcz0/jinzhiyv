<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use App\Utils\Cache;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class LoginController extends Controller
{
    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    public $validationFactory;

    public function login(RequestInterface $request)
    {
        $validator = $this->validationFactory->make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors()->all());
        }
        $response = $this->client->post($this->url . '/wechat.php/Vip/mobileLoginNew', [
            'form_params' => [
                'mobile_phone' => $request->input('phone'),
                'password' => $request->input('password'),
            ],
        ]);
        if ($response->getStatusCode() == 200) {
            $data = json_decode((string)$response->getBody(), true);
            if ($data['code'] == 1) {
                $data = $data['data'];
                $response = $this->client->post($this->url . '/wechat.php/Address/getMyAddress', [
                    'form_params' => [
                        'siv' => $data['siv'],
                        'stoken' => $data['stoken'],
                    ],
                ]);
                $address = json_decode((string)$response->getBody(), true);
                if ($address['code'] == 1) {
                    $data['address_id'] = $address['data'][0]['id'];
                    Cache::set('login', $data);
                    return $this->success($data, '成功登录');
                }
            } else {
                return $this->fail($data['message']);
            }
        } else {
            return $this->fail('登录失败');
        }
    }

    public function tokenLogin(RequestInterface $request)
    {
        $validator = $this->validationFactory->make($request->all(), [
            'siv' => 'required|string',
            'stoken' => 'required|string',
            'address_id' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors()->all());
        }
        $login = Cache::get('login');
        $login['siv'] = $request->input('siv');
        $login['stoken'] = $request->input('stoken');
        $login['address_id'] = $request->input('address_id');
        Cache::set('login', $login);
        return $this->success([], '成功登录');
    }
}
