<?php

namespace App\Task;

use App\Service\MailService;
use App\Utils\Cache;
use Carbon\Carbon;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Logger\LoggerFactory;

class Pick
{
    /**
     * @Inject
     * @var MailService
     */
    protected $service;
    protected $client;
    protected $log;
    protected $siv;
    protected $stoken;
    protected $address_id;

    public function __construct(ClientFactory $clientFactory, LoggerFactory $loggerFactory)
    {
        $this->client = $clientFactory->create();
        $this->log = $loggerFactory->get('log', 'test');
        $login = Cache::get('login');
        $this->siv = $login['siv'];
        $this->stoken = $login['stoken'];
        $this->address_id = $login['address_id'];
    }

    /**
     * @Crontab(name="AmPick", rule="* * 10 * * *", memo="降序循环秒杀", singleton=false)
     */
    public function amPick()
    {
        if (Cache::get('am_num') > 0) {
            if (date('Y-m-d 10:30:00') <= Carbon::now() && date('Y-m-d 11:45:00') >= Carbon::now()) {
                $response = $this->client->post('https://jzy.bjyush.com/wechat.php/Show/productlist', [
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
                    if (!is_array($data)) {
                        if ($data['code'] == 1) {
                            $page_num = $data['data']['data_list']['last_page'];
                            $goods = $data['data']['data_list']['data'];
                            for ($i = 2; $i <= $page_num; $i++) {
                                $response = $this->client->post('https://jzy.bjyush.com/wechat.php/Show/productlist', [
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
                                        foreach ($goods as $k => &$v) {
                                            if ($v['is_pub'] == 2) {
                                                unset($goods[$k]);
                                            }
                                        }
                                        if (!count($goods)) {
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
                                                    $this->log->info('ampick' . $response->getBody());
                                                    $data = json_decode((string)$response->getBody(), true);
                                                    if (is_array($data)) {
                                                        if ($data['code'] == 1) {
                                                            $this->service->push('2673362947@qq.com');
                                                            Cache::set('am_num', Cache::get('am_num') - 1);
                                                        }
                                                    }
                                                }
                                                if (Cache::get('am_num') <= 0) {
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @Crontab(name="PmPick", rule="* * 14 * * *", memo="降序循环秒杀", singleton=false)
     */
    public function pmPick()
    {
        if (Cache::get('pm_num') > 0) {
            if (date('Y-m-d 14:00:00') <= Carbon::now() && date('Y-m-d 15:15:00') >= Carbon::now()) {
                $response = $this->client->post('https://jzy.bjyush.com/wechat.php/Show/productlist', [
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
                    if (!is_array($data)) {
                        if ($data['code'] == 1) {
                            $page_num = $data['data']['data_list']['last_page'];
                            $goods = $data['data']['data_list']['data'];
                            for ($i = 2; $i <= $page_num; $i++) {
                                $response = $this->client->post('https://jzy.bjyush.com/wechat.php/Show/productlist', [
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
                                        foreach ($goods as $k => &$v) {
                                            if ($v['is_pub'] == 2) {
                                                unset($goods[$k]);
                                            }
                                        }
                                        if (!count($goods)) {
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
                                                    $this->log->info('pmpick' . $response->getBody());
                                                    $data = json_decode((string)$response->getBody(), true);
                                                    if (is_array($data)) {
                                                        if ($data['code'] == 1) {
                                                            $this->service->push('2673362947@qq.com');
                                                            Cache::set('pm_num', Cache::get('pm_num') - 1);
                                                        }
                                                    }
                                                }
                                                if (Cache::get('pm_num') <= 0) {
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
