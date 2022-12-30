<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Middleware;

use Hyperf\Redis\Redis;
use OnixSystemsPHP\HyperfAuth\SessionManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class WsSessionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private SessionManager $sessionManager,
        private Redis $redis,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        var_dump($request);

        try {
            $response = $handler->handle($request);
        } finally {
            // $this->sessionManager->end($session);
        }
        return $response;
    }
}
