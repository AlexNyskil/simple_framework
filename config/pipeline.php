<?php

use app\Http\Middleware\ErrorHandlerMiddleware;
use app\Http\Middleware\ProfilerMiddleware;
use Framework\Http\Middleware\DispatchMiddleware;
use Framework\Http\Middleware\RouteMiddleware;

/**
 * @var Framework\Http\Application $app
 */

$app->pipe(ProfilerMiddleware::class);
$app->pipe(ErrorHandlerMiddleware::class);
$app->pipe(RouteMiddleware::class);
$app->pipe(DispatchMiddleware::class);