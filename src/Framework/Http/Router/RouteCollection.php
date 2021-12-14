<?php

namespace Framework\Http\Router;

class RouteCollection
{
    private $routes = [];

    public function addRoute(RouteInterface $route)
    {
        $this->routes[] = $route;
    }

    public function add($name, $pattern, $handler, array $methods, array $tokens = []): void
    {
        $this->addRoute(new Route($name, $pattern, $handler, $methods, $tokens));
    }

    public function get($name, $pattern, $handler, array $tokens = []): void
    {
        $this->addRoute(new Route($name, $pattern, $handler, ['GET'], $tokens));
    }

    public function any($name, $pattern, $handler, array $tokens = []): void
    {
        $this->addRoute(new Route($name, $pattern, $handler, ['GET', 'POST'], $tokens));
    }

    public function post($name, $pattern, $handler, array $tokens = []): void
    {
        $this->addRoute(new Route($name, $pattern, $handler, ['POST'], $tokens));
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}