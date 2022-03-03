<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class IndexController extends Controller
{
    public function am()
    {

        $login = Cache::get('login');

        if (empty($login) || $login == []) {
            return $this->fail('请先登录');
        }

        $response = Http::post($this->url. '', [
            'page' => '1',
            'region_id' => '2',
            'svi' => $login['svi'],
            'token' => $login['token'],
        ]);

        $goods = [];

        $page_num = 1;

        if ($response->status() == 200) {
            $data = $response->json();
            if ($data['code'] == 1) {
                $page_num = $data['data']['data_list']['last_page'];
                $goods = $data['data']['data_list']['data'];
            } else {
                return $this->fail('获取商品失败');
            }
        } else {
            return $this->fail('获取商品失败');
        }

        for ($i=1; $i <= $page_num; $i++) {
            $response = Http::post($this->url. '', [
                'page' => $i,
                'region_id' => '2',
                'svi' => $login['svi'],
                'token' => $login['token'],
            ]);

            if ($response->status() == 200) {
                $data = $response->json();
                if ($data['code'] == 1) {
                    $goods = array_merge($goods, $data['data']['data_list']['data']);
                } else {
                    return $this->fail('获取商品失败');
                }
            } else {
                return $this->fail('获取商品失败');
            }
        }

        foreach ($goods as $k => $v) {
            if ($v['is_pub'] == 2) {
                unset($goods[$k]);
            }
            if ($v['price'] > 50000)  {
                unset($goods[$k]);
            }
        }

        $goods = array_multisort(array_column($goods, 'price'), SORT_DESC, $goods);

        Cache::put('am_max', $goods[0]['id']);

        return $this->success($goods, '获取商品成功');
    }

    public function pm()
    {
        $login = Cache::get('login');

        if (empty($login) || $login == []) {
            return $this->fail('请先登录');
        }

        $response = Http::post($this->url. '', [
            'page' => '1',
            'region_id' => '3',
            'svi' => $login['svi'],
            'token' => $login['token'],
        ]);

        $goods = [];

        $page_num = 1;

        if ($response->status() == 200) {
            $data = $response->json();
            if ($data['code'] == 1) {
                $page_num = $data['data']['data_list']['last_page'];
                $goods = $data['data']['data_list']['data'];
            } else {
                return $this->fail('获取商品失败');
            }
        } else {
            return $this->fail('获取商品失败');
        }

        for ($i=1; $i <= $page_num; $i++) {
            $response = Http::post($this->url. '/wechat.php/Show/productlist', [
                'page' => $i,
                'region_id' => '2',
                'svi' => $login['svi'],
                'token' => $login['token'],
            ]);

            if ($response->status() == 200) {
                $data = $response->json();
                if ($data['code'] == 1) {
                    $goods = array_merge($goods, $data['data']['data_list']['data']);
                } else {
                    return $this->fail('获取商品失败');
                }
            } else {
                return $this->fail('获取商品失败');
            }
        }

        foreach ($goods as $k => $v) {
            if ($v['is_pub'] == 2) {
                unset($goods[$k]);
            }
        }

        $goods = array_multisort(array_column($goods, 'price'), SORT_DESC, $goods);

        Cache::put('pm_max', $goods[0]['id']);

        return $this->success($goods, '获取商品成功');
    }

    public function supBuy()
    {
        if(!Cache::has('login')) {
            return $this->fail('请先登录');
        }
        $login = Cache::get('login');
        $buys = [];
        $error = [];
        if(Cache::has('am_max')) {
            $am_max = Cache::get('am_max');
            $response = Http::post($this->url. '/wechat.php/Show/productdetails', [
                'id' => $am_max,
                'svi' => $login['svi'],
                'token' => $login['token'],
            ]);
            if ($response->status() == 200) {
                $data = $response->json();
                if ($data['code'] == 1) {
                    $buys[] += $data['data'];
                } else {
                    $error[] += 'am code 获取商品失败';
                }
            } else {
                $error[] += 'am http获取商品失败';
            }
        }

        if(Cache::has('pm_max')) {
            $pm_max = Cache::get('pm_max');
            $response = Http::post($this->url. '/wechat.php/Show/productdetails', [
                'id' => $pm_max,
                'svi' => $login['svi'],
                'token' => $login['token'],
            ]);
            if ($response->status() == 200) {
                $data = $response->json();
                if ($data['code'] == 1) {
                    $buys[] += $data['data'];
                } else {
                    $error[] += 'pm code 获取商品失败';
                }
            } else {
                $error[] += 'pm http获取商品失败';
            }
        }

        if(!count($buys)) {
            return $this->fail('获取商品失败');
        }

        Cache::put('buy', $buys);

        return '一键抢购成功, 期间请勿登录账号. 程序自动抢购中';
    }

    public function buy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',

        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors()->all());
        }
        $buy = Cache::get('buy');
        // TODO: 获取商品列表



    }

    public function goodsInfo(int $id)
    {
        $response = Http::post($this->url. '/wechat.php/Show/productdetails', [
            'id' => $id,
            'svi' => $this->svi,
            'token' => $this->token,
        ]);
        if ($response->status() == 200) {
            $data = $response->json();
            if ($data['code'] == 1) {
                return $data['data'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
