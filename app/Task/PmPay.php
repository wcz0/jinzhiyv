<?php

namespace App\Task;

use App\Service\CacheService;
use App\Service\MailService;
use App\Utils\Cache;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Logger\LoggerFactory;

class PmPay
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
        $this->log = $loggerFactory->get('log', 'pm');
        $this->max = Cache::get('pm_num');
        $login = Cache::get('login');
        $this->siv = $login['siv'];
        $this->stoken = $login['stoken'];
        $this->address_id = $login['address_id'];
    }

    /**
     * /@Crontab(name="PmPay", rule="29,59 59 13 * * *", memo="最贵秒杀")
     */
    public function pm()
    {
        $id = Cache::get('pm_buy')['id'];
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
                $this->log->info('pmpay1: ' . $response->getBody());
                $data = json_decode((string)$response->getBody(), true);
                if (is_array($data)) {
                    if ($data['code'] == 1) {
                        Cache::set('pm_num', Cache::get('pm_num') - 1);
                        $this->service->push('2673362947@qq.com');
                        $this->cache_service->push();
                        break;
                    }
                }else{
                    break;
                }
            }
        }
    }

    /**
     * @Crontab(name="PmPay2", rule="59 59 13 * * *", memo="降序秒杀")
     */
    public function pm2()
    {
        usleep(500000);
        if (Cache::get('pm_num') > 0) {
            $goods = Cache::get('pm_goods');
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
                    $this->log->info('pmpay2: ' . $response->getBody());
                    $data = json_decode((string)$response->getBody(), true);
                    if (is_array($data)) {
                        if ($data['code'] == 1) {
                            Cache::set('pm_num', Cache::get('pm_num') - 1);
                            $this->service->push('2673362947@qq.com');
                            $this->cache_service->push();
                        }
                    }else{
                        break;
                    }
                }
                if (Cache::get('pm_num') <= 0) {
                    break;
                }
            }
        }
    }

    /**
     * /@Crontab(name="PmPay3", rule="* 0 14 * * *", memo="降序循环秒杀", singleton=false)
     */
    public function pm3()
    {
        if (Cache::get('pm_num') > 0) {
            $goods = Cache::get('pm_goods');
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
                    $this->log->info('pmpay2: ' . $response->getBody());
                    $data = json_decode((string)$response->getBody(), true);
                    if (is_array($data)) {
                        if ($data['code'] == 1) {
                            Cache::set('pm_num', Cache::get('pm_num') - 1);
                            $this->service->push('2673362947@qq.com');
                            $this->cache_service->push();
                        }
                    }else{
                        break;
                    }
                }
                if (Cache::get('pm_num') <= 0) {
                    break;
                }
            }
        }
    }
}
