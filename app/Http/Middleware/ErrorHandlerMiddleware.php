<?php

namespace app\Http\Middleware;

use Framework\Http\Response\Response;
use Framework\Http\Response\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    private $debug;

    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            if ($this->debug) {
                return (new Response())->withBody(new Stream($e->getMessage()))->withStatus(500);
            }

            return (new Response())->withStatus(500);
        }
    }
}