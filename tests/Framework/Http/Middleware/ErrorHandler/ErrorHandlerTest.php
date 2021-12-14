<?php

namespace Tests\Framework\Http\Middleware\ErrorHandler;

use app\Http\Middleware\ErrorHandlerMiddleware;
use Framework\Http\Request\Request;
use Framework\Http\Response\Response;
use Framework\Http\Response\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ErrorHandlerTest extends TestCase
{
    public function testNone(): void
    {
        $handler = new ErrorHandlerMiddleware(true);
        $response = $handler->process(new Request(), new CorrectAction());

        self::assertEquals('Content', $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    public function testException(): void
    {
        $handler = new ErrorHandlerMiddleware(true);
        $response = $handler->process(new Request(), new ErrorAction());

        self::assertEquals('Runtime Error', $response->getBody()->getContents());
        self::assertEquals(500, $response->getStatusCode());
    }
}

class CorrectAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return (new Response())->withStatus(200)->withBody(new Stream('Content'));
    }
}

class ErrorAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        throw new \RuntimeException('Runtime Error');
    }
}
