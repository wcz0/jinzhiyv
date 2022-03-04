<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', [App\Controller\IndexController::class, 'index']);

Router::get('/favicon.ico', function () {
    return '';
});

Router::post('/login', [\App\Controller\LoginController::class, 'login']);

// Router::addGroup(
//     '/api',
//     function(){
//         Router::get('/am', [\App\Controller\IndexController::class, 'am']);
//         Router::get('/pm', [\App\Controller\IndexController::class, 'pm']);
//         Router::get('/sup-buy', [\App\Controller\IndexController::class, 'supBuy']);
//         Router::post('/buy', [\App\Controller\IndexController::class, 'buy']);
//     },
//     ['middleware' => [\App\Middleware\Auth\Check::class]]
// );
