<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace OnixSystemsPHP\HyperfAuth\Test\Cases\Services;

use OnixSystemsPHP\HyperfAuth\AuthManager;
use OnixSystemsPHP\HyperfAuth\Contract\Authenticatable;
use OnixSystemsPHP\HyperfAuth\Service\LogoutUserService;
use OnixSystemsPHP\HyperfAuth\Test\Cases\AppTest;

/**
 * @internal
 * @coversNothing
 */
class LogoutUserServiceTest extends AppTest
{
    public function testMain(): void
    {
        $authenticatable = $this->createMock(Authenticatable::class);
        $authManager = $this->createMock(AuthManager::class);
        $authManager->method('user')->willReturn($authenticatable);
        $service = $this->getService(1);
        $service->run($authManager);
        $this->assertTrue(true);

        $authManager = $this->createMock(AuthManager::class);
        $service = $this->getService(0);
        $service->run($authManager);
        $this->assertTrue(true);
    }

    protected function getService(int $eventCount): LogoutUserService
    {
        return new LogoutUserService($this->getEventDispatcherMock($eventCount));
    }
}
