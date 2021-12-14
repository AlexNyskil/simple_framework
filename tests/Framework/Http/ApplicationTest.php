<?php

namespace Tests\Framework\Http;

use Framework\Http\Application;
use Framework\Http\Pipeline\MiddlewareResolver;
use Framework\Http\Request\Request;
use Framework\Http\Response\Response;
use Framework\Http\Response\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ApplicationTest extends TestCase
{
    /**
     * @var MiddlewareResolver
     */
    private $resolver;

    public function setUp()
    {
        parent::setUp();
        $this->resolver = new MiddlewareResolver(new DummyContainer());
    }

    public function testPipe(): void
    {
        $app = new Application($this->resolver, new DefaultHandler());

        $app->pipe(new Middleware1());
        $app->pipe(new Middleware2());

        $response = $app->run(new Request(), new Response());

        $this->assertJsonStringEqualsJsonString(
            json_encode(['middleware-1' => 1, 'middleware-2' => 2]),
            $response->getBody()->getContents()
        );
    }
}

class Middleware1 implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request->withAttribute('middleware-1', 1));
    }
}

class Middleware2 implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request->withAttribute('middleware-2', 2));
    }
}

class DefaultHandler
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return (new Response())->withBody(new Stream(json_encode($request->getAttributes())))
            ->withStatus(200);
    }
}
