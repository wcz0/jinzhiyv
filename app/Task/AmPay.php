<?php

namespace App\Task;

use App\Service\MailService;
use App\Utils\Cache;
use Carbon\Carbon;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Guzzle\HandlerStackFactory;
use Hyperf\Logger\LoggerFactory;

class AmPay
{
    /**
     * @Inject
     * @var MailService
     */
    protected $service;
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
        $this->max = Cache::get('day_max');
        $login = Cache::get('login');
        $this->siv = $login['siv'];
        $this->stoken = $login['stoken'];
        $this->address_id = $login['address_id'];
    }

    /**
     * @Crontab(name="AmPay", rule="29,59 29 10 * * *", memo="最贵秒杀")
     */
    public function am()
    {
        $id = Cache::get('am_buy')['id'];
        for ($i = 0; $i < 9; $i++) {
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
                        $this->service->push('2673362947@qq.com');
                        Cache::set('day_max', Cache::get('day_max') - 1);
                        break;
                    }
                }
            }
        }
    }

    /**
     * @Crontab(name="AmPay2", rule="59 29 10 * * *", memo="降序秒杀")
     */
    public function am2()
    {
        if (Cache::get('day_max') > 0) {
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
                            $this->service->push('2673362947@qq.com');
                            Cache::set('day_max', Cache::get('day_max') - 1);
                        }
                    }
                }
                if (Cache::get('day_max') <= 0) {
                    break;
                }
            }
        }
    }

    /**
     * @Crontab(name="AmPay3", rule="* 29 10 * * *", memo="降序循环秒杀", single=false)
     */
    public function am3()
    {
        if (Cache::get('day_max') > 0) {
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
                            $this->service->push('2673362947@qq.com');
                            Cache::set('day_max', Cache::get('day_max') - 1);
                        }
                    }
                }
                if (Cache::get('day_max') <= 0) {
                    break;
                }
            }
        }
    }
}
