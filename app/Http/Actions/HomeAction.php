<?php

namespace app\Http\Actions;

use Framework\Http\Response\Response;
use Framework\Http\Response\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HomeAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return (new Response())->withStatus(200)->withBody(new Stream('Home'));
    }
}