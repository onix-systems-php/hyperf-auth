<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Service;

use Hyperf\DbConnection\Annotation\Transactional;
use OnixSystemsPHP\HyperfAuth\Contract\TokenGuard;
use OnixSystemsPHP\HyperfAuth\DTO\AuthTokensDTO;
use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatableProvider;
use OnixSystemsPHP\HyperfCore\Contract\CorePolicyGuard;
use OnixSystemsPHP\HyperfCore\Service\Service;

#[Service]
class RefreshTokenService
{
    public function __construct(
        private CoreAuthenticatableProvider $authenticatableProvider,
        private ?CorePolicyGuard $policyGuard,
    ) {}

    #[Transactional(attempts: 1)]
    public function run(TokenGuard $jwtGuard): AuthTokensDTO
    {
        $dto = $jwtGuard->refresh();
        $this->policyGuard?->check('refresh_token', $this->authenticatableProvider->user());
        return $dto;
    }
}
