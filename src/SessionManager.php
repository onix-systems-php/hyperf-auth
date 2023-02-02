<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth;

use Hyperf\Context\Context;
use Hyperf\Contract\SessionInterface;
use Hyperf\Session\SessionManager as BaseSessionManager;
use OnixSystemsPHP\HyperfAuth\Contract\Authenticatable;
use OnixSystemsPHP\HyperfAuth\Contract\AuthenticatableProvider;
use Psr\Http\Message\ServerRequestInterface;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;

class SessionManager extends BaseSessionManager implements AuthenticatableProvider
{
    public function start(ServerRequestInterface $request): SessionInterface
    {
        $sessionId = $this->parseSessionId($request);
        return $this->startFromId($sessionId);
    }

    public function startFromId(?string $sessionId = null): SessionInterface
    {
        $session = new Session($this->getSessionName(), $this->buildSessionHandler(), $sessionId);
        if (! $session->start()) {
            throw new UnauthorizedException('Start session failed.');
        }
        $this->setSession($session);
        return $session;
    }

    public function isSessionStarted(): bool
    {
        return (bool) Context::get(SessionInterface::class)?->isStarted();
    }

    public function user(): Authenticatable|null
    {
        if ($this->isSessionStarted()) {
            return $this->getSession()->get('user');
        }
        return null;
    }
}
