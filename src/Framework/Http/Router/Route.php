<?php

namespace Framework\Http\Router;

use http\Exception\InvalidArgumentException;
use Psr\Http\Message\RequestInterface;

class Route implements RouteInterface
{
    public $name;
    public $pattern;
    public $handler;
    public $tokens;
    public $methods;

    public function __construct(
        $name,
        $pattern,
        $handler,
        array $methods,
        array $tokens = []
    )
    {
        $this->name = $name;
        $this->pattern = $pattern;
        $this->handler = $handler;
        $this->tokens = $tokens;
        $this->methods = $methods;
    }

    public function match(RequestInterface $request): ?Result
    {
        if ($this->methods && !in_array($request->getMethod(), $this->methods, true)) {
            return null;
        }

        $pattern = preg_replace_callback('~\{([^\}]+)\}~', function ($matches) {
            $argument = $matches[1];
            $replace = $this->tokens[$argument] ?? '[^}]+';

            return '(?P<' . $argument . '>' . $replace . ')';
        }, $this->pattern);

        $path = $request->getUriPath();

        if (!preg_match('~^' . $pattern . '$~i', $path, $matches)) {
            return null;
        }

        if (preg_match('~' . $pattern . '~', $path, $matches)) {
            return new Result($this->name, $this->handler, array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));
        }

        return null;
    }

    public function generate($name, array $arguments = []): ?string
    {
        if ($name !== $this->name) {
            return null;
        }

        $url = preg_replace_callback('~\{([^\}]+)\}~', function ($matches) use ($arguments) {
            $argument = $matches[1];

            if (!array_key_exists($argument, $arguments)) {
                throw new InvalidArgumentException('Missing parameters "' . $argument . '"');
            }

            return $arguments[$argument];
        }, $this->pattern);

        if ($url) {
            return $url;
        }

        return null;
    }
}