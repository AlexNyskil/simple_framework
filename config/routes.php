<?php

use app\Http\Actions\AboutAction;
use app\Http\Actions\CabinetAction;
use app\Http\Actions\HomeAction;
use app\Http\Middleware\BasicAuthMiddleware;
use Framework\Http\Container\Container;
use Framework\Http\Router\RouteCollection;

$container->set(RouteCollection::class, function (Container $container) {
    $routes = new RouteCollection();

    $routes->get('home', '/', HomeAction::class);
    $routes->get('about', '/about', AboutAction::class);
    $routes->get('cabinet', '/cabinet', [
        new BasicAuthMiddleware($container->get('config')['users']),
        CabinetAction::class
    ]);

    return $routes;
});