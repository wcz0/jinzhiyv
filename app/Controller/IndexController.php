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

use App\Service\MailService;
use App\Utils\Cache;
use Carbon\Carbon;
use GuzzleHttp\Client;

use Hyperf\Guzzle\HandlerStackFactory;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Di\Annotation\Inject;


class IndexController extends Controller
{

    public function am()
    {
        $login = Cache::get('login');
        $siv = $login['siv'];
        $stoken = $login['stoken'];
        $$address_id = $login['address_id'];
        $response = $this->client->post($this->url . '/wechat.php/Show/productlist', [
            'form_params' => [
                'page' => '1',
                'region_id' => '2',
                'siv' => $siv,
                'stoken' => $stoken,
            ],
        ]);
        $goods = [];
        $page_num = 1;
        if ($response->getStatusCode() == 200) {
            $data = json_decode((string)$response->getBody(), true);
            if (!is_array($data)) {
                return $this->fail('用户信息已过期, 请重新登录');
            }
            if ($data['code'] == 1) {
                $page_num = $data['data']['data_list']['last_page'];
                $goods = $data['data']['data_list']['data'];
            } else {
                return $this->fail('获取商品失败1');
            }
        } else {
            return $this->fail('获取商品失败2');
        }
        for ($i = 2; $i <= $page_num; $i++) {
            $response = $this->client->post($this->url . '/wechat.php/Show/productlist', [
                'form_params' => [
                    'page' => $i,
                    'region_id' => '2',
                    'siv' => $siv,
                    'stoken' => $stoken,
                ],
            ]);
            if ($response->getStatusCode() == 200) {
                $data = json_decode((string)$response->getBody(), true);
                if ($data['code'] == 1) {
                    $goods = array_merge($goods, $data['data']['data_list']['data']);
                } else {
                    return $this->fail('获取商品失败3');
                }
            } else {
                return $this->fail('获取商品失败4');
            }
        }
        foreach ($goods as $k => &$v) {
            if ($v['is_pub'] == 2) {
                unset($goods[$k]);
            }
            $v['price'] = (int)$v['price'];
        }
        if (!count($goods)) {
            return $this->fail('商品列表为空');
        }
        array_multisort(array_column($goods, 'price'), SORT_DESC, SORT_REGULAR, $goods);
        Cache::set('am_max', $this->goodsInfo($goods[0]['id']));
        Cache::set('am_goods', $goods);
        return $this->success($goods, '获取商品成功');
    }

    public function pm()
    {
        $login = Cache::get('login');
        $siv = $login['siv'];
        $stoken = $login['stoken'];
        $$address_id = $login['address_id'];
        $response = $this->client->post($this->url . '/wechat.php/Show/productlist', [
            'form_params' => [
                'page' => 1,
                'region_id' => 3,
                'siv' => $siv,
                'stoken' => $stoken,
            ],
        ]);
        $goods = [];
        $page_num = 1;
        if ($response->getStatusCode() == 200) {
            $data = json_decode((string)$response->getBody(), true);
            if (!is_array($data)) {
                return $this->fail('用户信息已过期, 请重新登录');
            }
            if ($data['code'] == 1) {
                $page_num = $data['data']['data_list']['last_page'];
                $goods = $data['data']['data_list']['data'];
            } else {
                return $this->fail('获取商品失败1');
            }
        } else {
            return $this->fail('获取商品失败2');
        }
        for ($i = 2; $i <= $page_num; $i++) {
            $response = $this->client->post($this->url . '/wechat.php/Show/productlist', [
                'form_params' => [
                    'page' => $i,
                    'region_id' => '3',
                    'siv' => $siv,
                    'stoken' => $stoken,
                ],
            ]);
            if ($response->getStatusCode() == 200) {
                $data = json_decode((string)$response->getBody(), true);
                if ($data['code'] == 1) {
                    $goods = array_merge($goods, $data['data']['data_list']['data']);
                } else {
                    return $this->fail('获取商品失败3');
                }
            } else {
                return $this->fail('获取商品失败4');
            }
        }
        foreach ($goods as $k => $v) {
            if ($v['is_pub'] == 2) {
                unset($goods[$k]);
            }
            $v['price'] = (int)$v['price'];
        }
        if (!count($goods)) {
            return $this->fail('商品列表为空');
        }
        array_multisort(array_column($goods, 'price'), SORT_DESC, SORT_REGULAR, $goods);
        Cache::set('pm_max', $this->goodsInfo($goods[0]['id']));
        Cache::set('pm_goods', $goods);
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

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    public function buy(RequestInterface $request)
    {
        $validator = $this->validationFactory->make($request->all(), [
            'id' => 'required',
            'time' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors()->all());
        }
        if ($request->input('time') == 'am') {
            Cache::set('am_buy', $this->goodsInfo($request->input('id')));
        } else {
            Cache::set('pm_buy', $this->goodsInfo($request->input('id')));
        }
        return $this->success([], '抢购特定商品成功, 期间请勿登录账号. 程序自动抢购中');
    }

    public function max(RequestInterface $request)
    {
        $validator = $this->validationFactory->make($request->all(), [
            'num' => 'required|integer|max:3',
            'time' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors()->all());
        }
        if ($request->input('time') == 'am') {
            Cache::set('am_num', $request->input('num'));
        } else {
            Cache::set('pm_num', $request->input('num'));
        }
        return $this->success([], '设置抢购限制成功');
    }

    public function goodsInfo($id)
    {
        $login = Cache::get('login');
        $siv = $login['siv'];
        $stoken = $login['stoken'];
        $$address_id = $login['address_id'];
        $response = $this->client->post($this->url . '/wechat.php/Show/productdetails', [
            'form_params' => [
                'id' => $id,
                'siv' => $siv,
                'stoken' => $stoken,
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


    /**
     * @Inject
     * @var MailService
     */
    protected $service;

    public function index()
    {
        $num = mt_rand(0, 20);
        return $num;
    }

    public function test()
    {
        // $response =$this->client->get('http://localhost:9051/');
        $response =$this->client->get('http://127.0.0.1:9501/');

        return $response->getBody();
    }

}
