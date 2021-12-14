<?php

namespace Framework\Http\Pipeline;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Next
{
    private $queue;
    private $default;

    public function __construct(\SplQueue $queue, $default)
    {
        $this->default = $default;
        $this->queue = $queue;
    }

    public function next(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if ($this->queue->isEmpty()) {
            #todo придумать что то для нахождения не только invoke
            return ($this->default)($request);
        }

        $current = $this->queue->dequeue();

        return $current($request, $response, function (ServerRequestInterface $request, ResponseInterface $response) {
            return $this->next($request, $response);
        });
    }
}