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
    protected $client;

    public function __construct()
    {
        $cf = new ClientFactory($this->container);
        $this->client = $cf->create();        
    }
}
