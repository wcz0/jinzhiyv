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
        $this->max = Cache::get('day_max');
        $login = Cache::get('login');
        $this->siv = $login['siv'];
        $this->stoken = $login['stoken'];
        $this->address_id = $login['address_id'];
    }

    /**
     * @Crontab(name="Test1", rule="5 17 * * *", memo="这是上午的定时任务", singleton=false)
     */
    public function am()
    {
        $this->log->info($this->siv);
    }

    /**
     * @Crontab(name="Test2", rule="7 17 * * *", memo="这是上午的定时任务", singleton=false)
     */
    public function am2()
    {
        $this->log->info($this->siv);
        
    }
}
