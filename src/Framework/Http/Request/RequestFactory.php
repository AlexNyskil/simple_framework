<?php

namespace Framework\Http\Request;

use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;

class RequestFactory
{
    public function __invoke(
        array $server = null,
        array $query = null,
        array $body = null,
        array $cookies = null,
        array $files = null
    ): ServerRequestInterface
    {
        $server  = $server ?: $_SERVER;
        $files   = $files ?: $_FILES;
        $headers = $this->getHeaders($server);

        return new Request(
            $server,
            $files,
            'php://input',
            $headers,
            $cookies ?: $_COOKIE,
            $query ?: $_GET,
            $body ?: $_POST,
            $this->getProtocolVersion($server)
        );
    }

    private function getHeaders(array $server)
    {
        $headers = [];
        foreach ($server as $key => $value) {
            // Apache prefixes environment variables with REDIRECT_
            // if they are added by rewrite rules
            if (strpos($key, 'REDIRECT_') === 0) {
                $key = substr($key, 9);

                // We will not overwrite existing variables with the
                // prefixed versions, though
                if (array_key_exists($key, $server)) {
                    continue;
                }
            }

            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = strtr(strtolower(substr($key, 5)), '_', '-');
                $headers[$name] = $value;
                continue;
            }

            if ($value && strpos($key, 'CONTENT_') === 0) {
                $name = 'content-' . strtolower(substr($key, 8));
                $headers[$name] = $value;
                continue;
            }
        }

        return $headers;
    }

    private function getProtocolVersion(array $server)
    {
        if (!isset($server['SERVER_PROTOCOL'])) {
            return '1.1';
        }

        if (!preg_match('#^(HTTP/)?(?P<version>[1-9]\d*(?:\.\d)?)$#', $server['SERVER_PROTOCOL'], $matches)) {
            throw new UnexpectedValueException(sprintf(
                'Unrecognized protocol version (%s)',
                $server['SERVER_PROTOCOL']
            ));
        }

        return $matches['version'];
    }
}