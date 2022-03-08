<?php

namespace App\Task;

use App\Utils\Cache;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Guzzle\HandlerStackFactory;

/**
 * @Crontab(name="PmPay", rule="* * * * * *", memo="这是下午的定时任务", callback="execute", enable="isEnable", singleton=false)
 */
class PmPay
{
    public function execute()
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
        $data = Cache::get('pm_buy');
        if ($data['ceshi_start_time'] <= Carbon::now()) {
            $i = 1;
            do {
                $response = $client->post('https://jzy.bjyush.com/wechat.php/Show/subpaymoney', [
                    'form_params' => [
                        'id' => $data['id'],
                        'siv' => $siv,
                        'stoken' => $stoken,
                        'address_id' => 394,
                        'pay_way' => 1,
                        'coupon_id' => '',
                    ],
                ]);
                if ($response->getStatusCode() == 200) {
                    $data = json_decode((string)$response->getBody(), true);
                    if (is_array($data)) {
                        if ($data['code'] == 1) {
                            $flag = false;
                        }
                    }
                }
                if ($i >= 5) {
                    $goods = Cache::get('pm_goods');
                            foreach ($goods as $v) {
                                $response = $client->post('https://jzy.bjyush.com/wechat.php/Show/subpaymoney', [
                                    'form_params' => [
                                        'id' => $v['id'],
                                        'siv' => $siv,
                                        'stoken' => $stoken,
                                        'address_id' => 394,
                                        'pay_way' => 1,
                                        'coupon_id' => '',
                                    ],
                                ]);
                                if ($response->getStatusCode() == 200) {
                                    $good_data = json_decode((string)$response->getBody(), true);
                                    if (is_array($good_data)) {
                                        if ($good_data['code'] == 1) {
                                            $flag = false;
                                        }
                                    }
                                }
                            }
                    $flag = false;
                }
                $i++;
            } while ($flag);
            Cache::delete('pm_buy');
            return true;
        }
        return false;
    }

    public function isEnable(): bool
    {
        if (Cache::has('pm_buy')) {
            return true;
        }
        return false;
    }

}