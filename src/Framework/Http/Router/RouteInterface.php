<?php

namespace Framework\Http\Router;

use Psr\Http\Message\RequestInterface;

interface RouteInterface
{
    public function match(RequestInterface $request): ?Result;
    public function generate($name, array $arguments = []): ?string;
}