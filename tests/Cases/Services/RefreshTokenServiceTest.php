<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Test\Cases\Services;

use OnixSystemsPHP\HyperfAuth\Contract\TokenGuard;
use OnixSystemsPHP\HyperfAuth\DTO\AuthTokensDTO;
use OnixSystemsPHP\HyperfAuth\Service\RefreshTokenService;
use OnixSystemsPHP\HyperfAuth\Test\Cases\AppTest;
use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatableProvider;
use OnixSystemsPHP\HyperfCore\Contract\CorePolicyGuard;
use OnixSystemsPHP\HyperfCore\Exception\BusinessException;

/**
 * @internal
 * @coversNothing
 */
class RefreshTokenServiceTest extends AppTest
{
    public function testMain()
    {
        $tokenGuard = $this->createMock(TokenGuard::class);
        $tokenGuard->expects($this->once())->method('refresh')->willReturn(AuthTokensDTO::make([
            'access_token' => 'access_token',
            'refresh_token' => 'refresh_token',
        ]));

        $service = $this->getService();
        $dto = $service->run($tokenGuard);
        $this->assertInstanceOf(AuthTokensDTO::class, $dto);
    }

    public function testDontValid()
    {
        $tokenGuard = $this->createMock(TokenGuard::class);
        $tokenGuard->expects($this->once())->method('refresh')->willReturn(AuthTokensDTO::make([
            'access_token' => 'access_token',
            'refresh_token' => 'refresh_token',
        ]));

        $service = $this->getService(false);
        $this->expectException(BusinessException::class);
        $service->run($tokenGuard);
        $this->assertTrue(true);
    }

    protected function getService($is_valid = true): RefreshTokenService
    {
        $coreAuthenticatableProvider = $this->createMock(CoreAuthenticatableProvider::class);
        $corePolicyGuard = $this->createMock(CorePolicyGuard::class);
        if (! $is_valid) {
            $corePolicyGuard->expects($this->once())->method('check')->willThrowException(new BusinessException());
        }
        return new RefreshTokenService(
            $coreAuthenticatableProvider,
            $corePolicyGuard
        );
    }
}
