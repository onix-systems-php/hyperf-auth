<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth;

use Hyperf\Contract\ConfigInterface;
use OnixSystemsPHP\HyperfAuth\Guards\JwtGuard;
use Qbhy\HyperfAuth\Authenticatable;
use Qbhy\HyperfAuth\AuthGuard;
use Qbhy\HyperfAuth\AuthManager as BaseAuthManager;

class AuthManager extends BaseAuthManager
{
    public function __construct(
        ConfigInterface $config,
        private SessionManager $sessionManager
    ) {
        parent::__construct($config);
        foreach ($this->config['guards'] as $name => $config) {
            $this->guard($name);
        }
    }

    public function user(): ?Authenticatable
    {
        $user = null;
        /** @var AuthGuard $guard */
        foreach ($this->getGuards() as $guard) {
            $user = $guard->user();
            if ($user instanceof Authenticatable) {
                break;
            }
        }
        return $user;
    }

    public function logout(): void
    {
        /** @var AuthGuard $guard */
        foreach ($this->getGuards() as $guard) {
            $guard->logout();
        }
        $this->sessionManager->getSession()->invalidate();
    }

    public function jwtGuard(): JwtGuard
    {
        /** @var JwtGuard $guard */
        $guard = $this->guard('jwt');
        return $guard;
    }
}
