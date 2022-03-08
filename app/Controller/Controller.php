<?php

declare(strict_types=1);

namespace App\Controller;

use App\Utils\Cache;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class Controller extends AbstractController
{
    public $url = 'https://jzy.bjyush.com';

    

    public function fail($msg)
    {
        return [
            'code' => 0,
            'status' => 'fail',
            'msg' => $msg,
        ];
    }

    public function success($data, $msg)
    {
        return [
            'code' => 1,
            'status' => 'success',
            'msg' => $msg,
            'data' => $data,
        ];
    }

    /**
     * @var \Hyperf\Guzzle\ClientFactory
     */
    public $clientFactory;

    protected $siv;
    protected $stoken;

    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
        if (Cache::has('login')) {
            $login = Cache::get('login');
            $this->siv = $login['siv'];
            $this->stoken = $login['stoken'];
        }
        
    }
}
