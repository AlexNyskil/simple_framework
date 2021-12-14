<?php

namespace Framework\Http\Pipeline;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareResolver
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve($handler): callable
    {
        if (\is_array($handler)) {
            return $this->createPipe($handler);
        }

        if (\is_string($handler) && $this->container->has($handler)) {
            return function (ServerRequestInterface $request, ResponseInterface $response, $next) use ($handler) {
                $middleware = $this->resolve($this->container->get($handler));

                return $middleware($request, $response, $next);
            };
        }

        if ($handler instanceof MiddlewareInterface) {
            return function(ServerRequestInterface $request, ResponseInterface $response, $next) use ($handler) {
                   return $handler->process($request, new HandlerWrapper($response, $next));
            };
        }

        if ($handler instanceof RequestHandlerInterface) {
            return function(ServerRequestInterface $request, ResponseInterface $response, $next) use ($handler) {
                return $handler->handle($request);
            };
        }

        if (\is_object($handler)) {
            $reflection = new \ReflectionObject($handler);
            if ($reflection->hasMethod('__invoke')) {
                $method = $reflection->getMethod('__invoke');
                $parameters = $method->getParameters();
                if (\count($parameters) === 2 && $parameters[1]->isCallable()) {
                    return function (ServerRequestInterface $request, ResponseInterface $response, $next) use ($handler) {
                        return $handler($request, $next);
                    };
                }

                return $handler;
            }
        }

        throw new UnknownMiddlewareTypeException($handler);
    }

    private function createPipe(array $handlers): Pipeline
    {
        $pipeline = new Pipeline();
        foreach ($handlers as $handler) {
            $pipeline->pipe($this->resolve($handler));
        }

        return $pipeline;
    }
}
