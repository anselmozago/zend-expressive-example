<?php

namespace Api;

use Zend\Stratigility\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class Auth implements MiddlewareInterface
{
    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        if(! $request->hasHeader('authorization')){
            //return $response->withStatus(401);
        }

        if (!$this->isValid($request)) {
            //return $response->withStatus(401);
        }

        return $out($request, $response);
    }

    
    private function isValid(Request $request)
    {
        $token = $request->getHeader('authorization');
        if ($token[0] == '8CPpn3kesJhZH9jt3jStFGSRbBz3BCVDwVw9hNXgGcKDMQg7T4Lt2gUSDQm7qd4j') {
            return true;
        }

        return false;
    }

}