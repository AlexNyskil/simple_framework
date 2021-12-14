<?php

use Framework\Http\Container\Container;

$container = new Container();
$container->set('config', require __DIR__ . '/parameters.php');
require __DIR__ .'/definitions.php';
require __DIR__ .'/routes.php';

return $container;