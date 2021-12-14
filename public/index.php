<?php

use Framework\Http\Application;
use Framework\Http\Request\RequestFactory;
use Framework\Http\Response\Emitter;
use Framework\Http\Response\Response;

chdir(dirname(__DIR__));

require 'vendor/autoload.php';
$container = require 'config/container.php';

/**
 * @var Framework\Http\Application $app
 */
$app = $container->get(Application::class);
require 'config/pipeline.php';

$request = (new RequestFactory())();
$response = $app->run($request, new Response());

$emitter = new Emitter();
$emitter->emit($response);





