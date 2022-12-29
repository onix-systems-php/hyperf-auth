<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Service;

use Hyperf\DbConnection\Annotation\Transactional;
use OnixSystemsPHP\HyperfAuth\Contract\TokenGuard;
use OnixSystemsPHP\HyperfAuth\DTO\AuthTokensDTO;
use OnixSystemsPHP\HyperfCore\Service\Service;

#[Service]
class RefreshTokenService
{
    #[Transactional(attempts: 1)]
    public function run(TokenGuard $jwtGuard): AuthTokensDTO
    {
        return $jwtGuard->refresh();
    }
}
