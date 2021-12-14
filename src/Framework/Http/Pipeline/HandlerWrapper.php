<?php

namespace Framework\Http\Pipeline;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HandlerWrapper implements RequestHandlerInterface
{
    private $callback;
    private $response;

    public function __construct(ResponseInterface $response, callable $callback)
    {
        $this->callback = $callback;
        $this->response = $response;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return ($this->callback)($request, $this->response);
    }
}