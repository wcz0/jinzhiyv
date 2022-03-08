<?php

namespace App\Task;

use App\Utils\Cache;
use Carbon\Carbon;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\HandlerStackFactory;

/**
 * @Crontab(name="AmPay", rule="* * * * * *", memo="这是上午的定时任务", callback="execute", singleton=false, enable="isEnable")
 */
class AmPay
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
        $address_id = $login['address_id'];
        $data = Cache::get('am_buy');
        if ($data['ceshi_start_time'] <= Carbon::now()->addMinutes(1)) {
            $i = 1;
            do {
                $response = $client->post('https://jzy.bjyush.com/wechat.php/Show/subpaymoney', [
                    'form_params' => [
                        'id' => $data['id'],
                        'siv' => $siv,
                        'stoken' => $stoken,
                        'pay_way' => 1,
                        'address_id' => $address_id,
                        'coupon_id' => '',
                    ],
                ]);
                if ($response->getStatusCode() == 200) {
                    $data = json_decode((string)$response->getBody(), true);
                    if (is_array($data)) {
                        if ($data['code'] == 1) {
                            $flag = false;
                        }
                        $goods = Cache::get('am_goods');
                        foreach ($goods as $v) {
                            $response = $client->post('https://jzy.bjyush.com/wechat.php/Show/subpaymoney', [
                                'form_params' => [
                                    'id' => $v['id'],
                                    'siv' => $siv,
                                    'stoken' => $stoken,
                                    'pay_way' => 1,
                                    'address_id' => $address_id,
                                    'coupon_id' => '',
                                ],
                            ]);
                            if ($response->getStatusCode() == 200) {
                                if (is_array($data)) {
                                    if ($data['code'] == 1) {
                                        $flag = false;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                if ($i >= 10) {
                    $flag = false;
                }
                $i++;
            } while ($flag);
            Cache::delete('am_buy');
            return true;
        }
        return false;
    }

    public function isEnable(): bool
    {
        if (Cache::has('am_buy')) {
            return true;
        }
        return false;
    }
}
