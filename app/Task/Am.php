<?php

namespace App\Task;

use App\Utils\Cache;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Guzzle\HandlerStackFactory;

/**
 * @Crontab(name="Buy", rule="* * * * * *", callback="execute", memo="这是一个示例的定时任务")
 */
class Am
{
    public $client;

    public $url = 'https://jzy.bjyush.com';

    protected $siv;
    protected $stoken;

    public function __construct()
    {
        $factory = new HandlerStackFactory();
        $stack = $factory->create();
        $client = make(Client::class, [
            'config' => [
                'handler' => $stack,
            ],
        ]);
        $login = Cache::get('login');
        $this->client = $client;
        $this->siv = $login['siv'];
        $this->stoken = $login['stoken'];
    }
    /**
     * @Inject()
     * @var \Hyperf\Contract\StdoutLoggerInterface
     */
    public function execute()
    {
        if (Cache::has('am_buy')) {
            return false;
        }
        $pm = Cache::get('am_buy');
        if($pm['ceshi_start_time'] >= Carbon::now()->addSeconds(-1)){
            do {
                $response = $this->client->post($this->url . '/wechat.php/Show/productbuy', [
                    'form_params' => [
                        'id' => $pm['id'],
                        'siv' => $this->siv,
                        'stoken' => $this->stoken,
                    ],
                ]);
                if ($response->getStatusCode() == 200) {
                    $data = json_decode((string)$response->getBody(), true);
                    if ($data['code'] == 1) {
                        $flag = false;
                    }
                }
            } while ($flag);
            
        }
        Cache::delete('am_buy');
        return true;
    }
}