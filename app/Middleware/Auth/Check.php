<?php

declare(strict_types=1);

namespace App\Middleware\Auth;

use App\Utils\Cache;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Check implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $response;

    public function __construct(ContainerInterface $container,HttpResponse $response)
    {
        $this->container = $container;
        $this->response = $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(Cache::has('login')){
            return $handler->handle($request);
        }
        return $this->response->json([
            'code' => 0,
            'status' => 'fail',
            'msg' => '请先登录',
        ]);
    }
}