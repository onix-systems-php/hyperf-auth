<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Middleware;

use Carbon\Carbon;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\SessionInterface;
use Hyperf\HttpMessage\Cookie\Cookie;
use OnixSystemsPHP\HyperfAuth\AuthManager;
use OnixSystemsPHP\HyperfAuth\SessionManager;
use OnixSystemsPHP\HyperfCore\Constants\Time;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuthManager $authManager,
        private ConfigInterface $config,
        private SessionManager $sessionManager,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (! str_contains($request->getUri()->getPath(), 'auth/refresh')) {
            $this->authManager->user();
        }

        $session = $this->sessionManager->isSessionStarted()
            ? $this->sessionManager->getSession()
            : $this->sessionManager->start($request);

        try {
            $response = $handler->handle($request);
        } finally {
            $this->sessionManager->end($session);
        }
        return $this->addCookieToResponse($request, $response, $session);
    }

    private function getCookieExpirationDate(): int
    {
        if ($this->config->get('session.options.expire_on_close')) {
            $expirationDate = 0;
        } else {
            $expireSeconds = $this->config->get('session.options.cookie_lifetime', 5 * Time::HOUR);
            $expirationDate = Carbon::now()->addSeconds($expireSeconds)->getTimestamp();
        }
        return $expirationDate;
    }

    private function addCookieToResponse(
        ServerRequestInterface $request,
        ResponseInterface $response,
        SessionInterface $session
    ): ResponseInterface {
        $uri = $request->getUri();
        $path = '/';
        $secure = strtolower($uri->getScheme()) === 'https';
        $domain = $this->config->get('session.options.domain') ?? $uri->getHost();

        $cookie = new Cookie(
            $session->getName(),
            $session->getId(),
            $this->getCookieExpirationDate(),
            $path,
            $domain,
            $secure,
            true
        );
        if (! method_exists($response, 'withCookie')) {
            return $response->withHeader('Set-Cookie', (string) $cookie);
        }
        return $response->withCookie($cookie);
    }
}
