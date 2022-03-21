<?php

namespace App\Task;

use App\Service\CacheService;
use App\Service\MailService;
use App\Utils\Cache;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Logger\LoggerFactory;

class AmPay
{
    /**
     * @Inject
     * @var MailService
     */
    protected $service;
    /**
     * @Inject
     * @var CacheService
     */
    protected $cache_service;
    protected $client;
    protected $log;
    protected $max;
    protected $siv;
    protected $stoken;
    protected $address_id;

    public function __construct(ClientFactory $clientFactory, LoggerFactory $loggerFactory)
    {
        $this->client = $clientFactory->create();
        $this->log = $loggerFactory->get('log', 'am');
        $this->max = Cache::get('am_num');
        $login = Cache::get('login');
        $this->siv = $login['siv'];
        $this->stoken = $login['stoken'];
        $this->address_id = $login['address_id'];
    }

    /**
     * /@Crontab(name="AmPay", rule="29,59 29 10 * * *", memo="最贵秒杀")
     */
    public function am()
    {
        $id = Cache::get('am_buy')['id'];
        for ($i = 0; $i < 7; $i++) {
            $response = $this->client->post('https://jzy.bjyush.com/wechat.php/Show/subpaymoney', [
                'form_params' => [
                    'id' => $id,
                    'siv' => $this->siv,
                    'stoken' => $this->stoken,
                    'pay_way' => 1,
                    'address_id' => $this->address_id,
                    'coupon_id' => '',
                ],
            ]);
            if ($response->getStatusCode() == 200) {
                $this->log->info('ampay1: ' . $response->getBody());
                $data = json_decode((string)$response->getBody(), true);
                if (is_array($data)) {
                    if ($data['code'] == 1) {
                        Cache::set('am_num', Cache::get('am_num') - 1);
                        $this->service->push('2673362947@qq.com');
                        $this->cache_service->push();
                        break;
                    }
                } else {
                    break;
                }
            }
        }
    }

    /**
     * @Crontab(name="AmPay2", rule="59 29 10 * * *", memo="降序秒杀")
     */
    public function am2()
    {
        if ($this->max > 0) {
            $goods = Cache::get('am_goods');
            foreach ($goods as $v) {
                $response = $this->client->post('https://jzy.bjyush.com/wechat.php/Show/subpaymoney', [
                    'form_params' => [
                        'id' => $v['id'],
                        'siv' => $this->siv,
                        'stoken' => $this->stoken,
                        'pay_way' => 1,
                        'address_id' => $this->address_id,
                        'coupon_id' => '',
                    ],
                ]);
                if ($response->getStatusCode() == 200) {
                    $this->log->info('ampay2: ' . $response->getBody());
                    $data = json_decode((string)$response->getBody(), true);
                    if (is_array($data)) {
                        if ($data['code'] == 1) {
                            Cache::set('am_num', Cache::get('am_num') - 1);
                            $this->service->push('2673362947@qq.com');
                        $this->cache_service->push();
                        }
                    } else {
                        break;
                    }
                }
                if (Cache::get('am_num') <= 0) {
                    break;
                }
            }
        }
    }

    /**
     * /@Crontab(name="AmPay3", rule="* 30 10 * * *", memo="降序循环秒杀", singleton=false)
     */
    public function am3()
    {
        if ($this->max > 0) {
            $goods = Cache::get('am_goods');
            foreach ($goods as $v) {
                $response = $this->client->post('https://jzy.bjyush.com/wechat.php/Show/subpaymoney', [
                    'form_params' => [
                        'id' => $v['id'],
                        'siv' => $this->siv,
                        'stoken' => $this->stoken,
                        'pay_way' => 1,
                        'address_id' => $this->address_id,
                        'coupon_id' => '',
                    ],
                ]);
                if ($response->getStatusCode() == 200) {
                    $this->log->info('ampay3: ' . $response->getBody());
                    $data = json_decode((string)$response->getBody(), true);
                    if (is_array($data)) {
                        if ($data['code'] == 1) {
                            Cache::set('am_num', Cache::get('am_num') - 1);
                            $this->service->push('2673362947@qq.com');
                        $this->cache_service->push();
                        }
                    } else {
                        break;
                    }
                }
                if (Cache::get('am_num') <= 0) {
                    break;
                }
            }
        }
    }
}
