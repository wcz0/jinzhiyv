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
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Di\Annotation\Inject;


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
                return $this->fail('获取商品失败1');
            }
        } else {
            return $this->fail('获取商品失败2');
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
            if ($v['price'] > 50000) {
                unset($goods[$k]);
            }
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
        $client = $this->clientFactory->create();
        $response = $client->request('POST', $this->url . '/wechat.php/Show/productlist', [
            'form_params' => [
                'page' => 1,
                'region_id' => 3,
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
                return $this->fail('获取商品失败1');
            }
        } else {
            return $this->fail('获取商品失败2');
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
            if ($v['price'] > 50000) {
                unset($goods[$k]);
            }
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
        if($request->input('time') == 'am'){
            Cache::set('am_buy', $this->goodsInfo($request->input('id')));
        }else{
            Cache::set('pm_buy', $this->goodsInfo($request->input('id')));
        }
        return $this->success([], '抢购特定商品成功, 期间请勿登录账号. 程序自动抢购中');
    }

    public function goodsInfo($id)
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
        $factory = new HandlerStackFactory();
        $stack = $factory->create();
        $client = make(Client::class, [
            'config' => [
                'handler' => $stack,
            ],
        ]);
        $login = Cache::get('login');
        $siv = $login['siv'];
        $stoken = $login['stoken'];
        return $login['address_id'];
        $data = Cache::get('pm_buy');
        if ($data['ceshi_start_time'] <= Carbon::now()->addSeconds(-1)) {
            $i = 1;
            do {
                $response = $client->post('https://jzy.bjyush.com/wechat.php/Show/productbuy', [
                    'form_params' => [
                        'id' => $data['id'],
                        'siv' => $siv,
                        'stoken' => $stoken,
                    ],
                ]);
                if ($response->getStatusCode() == 200) {
                    $data = json_decode((string)$response->getBody(), true);
                    if (is_array($data)) {
                        return $data;
                        if ($data['code'] == 1) {
                            $flag = false;
                        }
                        $goods = Cache::get('pm_goods');
                        foreach ($goods as $v) {
                            $response = $client->post('https://jzy.bjyush.com/wechat.php/Show/productbuy', [
                                'form_params' => [
                                    'id' => $v['id'],
                                    'siv' => $siv,
                                    'stoken' => $stoken,
                                ],
                            ]);
                            if ($response->getStatusCode() == 200) {
                                if (is_array($data)) {
                                    if ($data['code'] == 1) {
                                        $flag = false;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($i >= 5) {
                    $flag = false;
                }
                $i++;
            } while ($flag);
            return true;
        }
        return false;
    }
}
