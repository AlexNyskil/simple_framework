<?php

namespace app\Http\Actions;

use Framework\Http\Response\Response;
use Framework\Http\Response\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CabinetAction implements RequestHandlerInterface
{
    private $users;

    public function __construct(array $users)
    {
        $this->users = $users;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $username = $request->getServerParams()['PHP_AUTH_USER'] ?? null;
        $password = $request->getServerParams()['PHP_AUTH_PW'] ?? null;

        if (!empty($password) && !empty($username)) {
            foreach ($this->users as $name => $pass) {
                if ($name == $username && $pass == $password) {
                    return (new Response())->withStatus(200)->withBody(new Stream('Cabinet'));
                }
            }
        }

        return (new Response())->withStatus(401)->withHeader('WWW-Authenticate' ,'Basic realm=Restricted area');
    }
}