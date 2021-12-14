<?php

namespace Framework\Http\Middleware;

use Framework\Http\Pipeline\MiddlewareResolver;
use Framework\Http\Response\Response;
use Framework\Http\Router\Result;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DispatchMiddleware implements MiddlewareInterface
{
    private $resolver;
    private $response;

    public function __construct(MiddlewareResolver $resolver)
    {
        $this->resolver = $resolver;
        $this->response = new Response();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$result = $request->getAttribute(Result::class)) {
            return $handler->handle($request);
        }

        $middleware = $this->resolver->resolve($result->getHandler());

        return $middleware($request, $this->response, $handler);
    }
}