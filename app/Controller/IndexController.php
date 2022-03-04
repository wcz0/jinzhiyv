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
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Guzzle\HandlerStackFactory;
use Hyperf\HttpServer\Contract\RequestInterface;

class IndexController extends Controller
{

    public function am()
    {
        $client = $this->clientFactory->create();
        $response = $client->request('POST', $this->url . '/wechat.php/Show/productlist', [
            'form_params' => [
                'page' => '1',
                'region_id' => '2',
                'siv' => $this->siv,
                'stoken' => $this->stoken,
            ],
        ]);
        $goods = [];
        $page_num = 1;
        if ($response->getStatusCode() == 200) {
            $data = json_decode((string)$response->getBody(), true);
            if ($data['code'] == 1) {
                $page_num = $data['data']['data_list']['last_page'];
                $goods = $data['data']['data_list']['data'];
            } else {
                return $this->fail('获取商品失败');
            }
        } else {
            return $this->fail('获取商品失败');
        }
        for ($i = 1; $i <= $page_num; $i++) {
            $client = $this->clientFactory->create();
            $response = $client->request('POST', $this->url . '/wechat.php/Show/productlist', [
                'form_params' => [
                    'page' => $i,
                    'region_id' => '2',
                    'siv' => $this->siv,
                    'stoken' => $this->stoken,
                ],
            ]);
            if ($response->getStatusCode() == 200) {
                $data = json_decode((string)$response->getBody(), true);
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
            if ($v['price'] > 50000) {
                unset($goods[$k]);
            }
        }
        $goods = array_multisort(array_column($goods, 'price'), SORT_DESC, $goods);
        Cache::set('am_max', $this->goodsInfo($goods[0]['id']));
        Cache::set('goods_am', $goods);
        return $this->success($goods, '获取商品成功');
    }

    public function pm()
    {
        $client = $this->clientFactory->create();
        $response = $client->request('POST', $this->url . '/wechat.php/Show/productlist', [
            'form_params' => [
                'page' => '1',
                'region_id' => '3',
                'siv' => $this->siv,
                'stoken' => $this->stoken,
            ],
        ]);
        $goods = [];
        $page_num = 1;
        if ($response->getStatusCode() == 200) {
            $data = json_decode((string)$response->getBody(), true);
            if ($data['code'] == 1) {
                $page_num = $data['data']['data_list']['last_page'];
                $goods = $data['data']['data_list']['data'];
            } else {
                return $this->fail('获取商品失败');
            }
        } else {
            return $this->fail('获取商品失败');
        }
        for ($i = 1; $i <= $page_num; $i++) {
            $client = $this->clientFactory->create();
            $response = $client->request('POST', $this->url . '/wechat.php/Show/productlist', [
                'form_params' => [
                    'page' => $i,
                    'region_id' => '3',
                    'siv' => $this->siv,
                    'stoken' => $this->stoken,
                ],
            ]);
            if ($response->getStatusCode() == 200) {
                $data = json_decode((string)$response->getBody(), true);
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
        Cache::set('pm_max', $this->goodsInfo($goods[0]['id']));
        Cache::set('goods_pm', $goods);
        return $this->success($goods, '获取商品成功');
    }

    public function supBuy()
    {
        if (!Cache::has('am_max')) {
            return $this->fail('请先获取商品');
        }
        if (!Cache::has('pm_max')) {
            return $this->fail('请先获取商品');
        }
        $am_buy = Cache::get('am_max');
        $pm_buy = Cache::get('pm_max');
        Cache::set('am_buy', $am_buy);
        Cache::set('pm_buy', $pm_buy);
        return $this->success([], '一键抢购成功, 期间请勿登录账号. 程序自动抢购中');
    }

    public function buy(RequestInterface $request)
    {
        $validator = $this->validationFactory->make($request->all(), [
            'id' => 'required',
            'time' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors()->all());
        }
        if($request->input('time') == 'am'){
            Cache::set('am_buy', $this->goodsInfo($request->input('id')));
        }else{
            Cache::set('pm_buy', $this->goodsInfo($request->input('id')));
        }
        return $this->success([], '抢购特定商品成功, 期间请勿登录账号. 程序自动抢购中');
    }

    public function goodsInfo(int $id)
    {
        $client = $this->clientFactory->create();
        $response = $client->request('POST', $this->url . '/wechat.php/Show/productdetails', [
            'form_params' => [
                'id' => $id,
                'siv' => $this->siv,
                'stoken' => $this->stoken,
            ],
        ]);
        if ($response->getStatusCode() == 200) {
            $data = json_decode((string)$response->getBody(), true);
            if ($data['code'] == 1) {
                return $data['data'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function index()
    {
        $client = $this->clientFactory->create();
        $response = $client->get('https://www.baidu.com');
        return (string)$response->getBody();
    }
}
