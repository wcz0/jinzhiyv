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

class Test
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
        $this->log = $loggerFactory->get('log', 'test');
        $this->max = Cache::get('am_num');
        $login = Cache::get('login');
        $this->siv = $login['siv'];
        $this->stoken = $login['stoken'];
        $this->address_id = $login['address_id'];
    }

    /**
     * //@Crontab(name="Test1", rule="46 10 * * *", memo="这是上午的定时任务", singleton=false)
     */
    public function am()
    {
        for ($i = 0; $i < 50; $i++) {
            $this->log->info($i);
            if ($i == 10) {
                break;
            }
        }
    }

    /**
     * /@Crontab(name="Test2", rule="11 12 * * *", memo="这是上午的定时任务", singleton=false)
     */
    public function am2()
    {

        if ($this->max > 0) {
            $goods = Cache::get('am_goods');
            for ($i = 0; $i < 100; $i++) {
                $response = $this->client->get('http://localhost:9501/');
                if ($response->getStatusCode() == 200) {
                    $this->log->info('ampay2: ' . $response->getBody());
                    $data = json_decode((string)$response->getBody(), true);
                    if ($data == 1) {
                        $this->log->info($i);
                        Cache::set('am_num', Cache::get('am_num') - 1);
                    }
                }
                if (Cache::get('am_num') <= 0) {
                    break;
                }
            }
        }
    }
}
