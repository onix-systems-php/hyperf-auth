<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Contract;

use OnixSystemsPHP\HyperfAuth\DTO\AuthTokensDTO;
use Qbhy\HyperfAuth\Authenticatable as BaseAuthenticatable;
use Qbhy\HyperfAuth\AuthGuard;

interface TokenGuard extends AuthGuard
{
    public function login(BaseAuthenticatable $user): AuthTokensDTO;

    public function refresh(): AuthTokensDTO;

    public function getAccessToken(): ?string;

    public function getRefreshToken(): ?string;
}
