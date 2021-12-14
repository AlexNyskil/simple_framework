<?php

namespace Framework\Http\Pipeline;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Pipeline
{
    private $queue;

    public function __construct()
    {
        $this->queue = new \SplQueue();
    }

    public function pipe($middleware): void
    {
        $this->queue->enqueue($middleware);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $default): ResponseInterface
    {
        $delegate = new Next(clone $this->queue, $default);

        return $delegate->next($request, $response);
    }
}