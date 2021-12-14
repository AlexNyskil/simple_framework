<?php

use app\Http\Middleware\ErrorHandlerMiddleware;
use app\Http\Middleware\NotFoundHandler;
use app\Http\Middleware\ProfilerMiddleware;
use Framework\Http\Application;
use Framework\Http\Container\Container;
use Framework\Http\Middleware\DispatchMiddleware;
use Framework\Http\Middleware\RouteMiddleware;
use Framework\Http\Pipeline\MiddlewareResolver;
use Framework\Http\Router\RouteCollection;
use Framework\Http\Router\Router;

$container->set(Application::class, function (Container $container) {
    return new Application($container->get(MiddlewareResolver::class), new NotFoundHandler());
});

$container->set(ErrorHandlerMiddleware::class, function(Container $container) {
    return new ErrorHandlerMiddleware($container->get('config')['debug']);
});

$container->set(MiddlewareResolver::class, function (Container $container) {
    return new MiddlewareResolver($container);
});

$container->set(RouteMiddleware::class, function (Container $container) {
    return new RouteMiddleware($container->get(Router::class));
});

$container->set(DispatchMiddleware::class, function (Container $container) {
    return new DispatchMiddleware($container->get(MiddlewareResolver::class));
});

$container->set(ProfilerMiddleware::class, function (Container $container) {
    return new ProfilerMiddleware();
});

$container->set(Router::class, function(Container $container) {
    $routes = $container->get(RouteCollection::class);

    return new Router($routes);
});