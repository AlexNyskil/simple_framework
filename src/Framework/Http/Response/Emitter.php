<?php

namespace Framework\Http\Response;

use Psr\Http\Message\ResponseInterface;

class Emitter implements EmitterInterface
{
    public function emit(ResponseInterface $response)
    {
        header(sprintf('HTTP/%s %d %s', $response->getProtocolVersion(), $response->getStatusCode(), $response->getReasonPhrase()));

        foreach ($response->getHeaders() as $name => $value) {
                header(sprintf('%s %d', $name, $value), false);
        }

        echo $response->getBody()->getContents();
    }
}