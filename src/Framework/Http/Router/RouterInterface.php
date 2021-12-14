<?php

namespace Framework\Http\Router;

use Psr\Http\Message\RequestInterface;

interface RouterInterface
{
    public function match(RequestInterface $request): Result;
    public function generate($name, array $params = []): string;
}