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

use Hyperf\Database\Model\ModelNotFoundException;
use OnixSystemsPHP\HyperfAuth\Model\UserSocialite;
use OnixSystemsPHP\HyperfAuth\Repository\UserSocialiteRepository;
use OnixSystemsPHP\HyperfAuth\Service\DeleteSocialiteService;
use OnixSystemsPHP\HyperfAuth\Test\Cases\AppTest;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;

/**
 * @internal
 * @coversNothing
 */
class DeleteSocialiteServiceTest extends AppTest
{
    protected function setUp(): void
    {
        $this->createContainer();
        parent::setUp();
    }

    public function testMain()
    {
        $userSocialite = new UserSocialite([
            'id' => 1,
            'email' => 'test@test.com',
            'provider_id' => 21,
            'provider_name' => 'fakeName',
            'user_id' => 2,
        ]);

        $service = $this->getService($userSocialite, 1, 1);
        $service->run('fakeName', 1);
        $this->assertTrue(true);
    }

    public function testWhereUserIsEmpty()
    {
        $service = $this->getService(null, 0, 0);
        try {
            $service->run('fakeName', 1);
            $this->fail();
        } catch (ModelNotFoundException) {
            $this->assertTrue(true);
        }
    }

    protected function getService(
        ?UserSocialite $userSocialite,
        int $eventCount,
        int $deleteMethodCount,
    ): DeleteSocialiteService {
        $userSocialiteRepository = $this->createMock(UserSocialiteRepository::class);
        $userSocialiteRepository->expects(new InvokedCount($deleteMethodCount))
            ->method('delete')
            ->willReturn(true);
        if ($userSocialite) {
            $userSocialiteRepository->method('getByUserProvider')->willReturn($userSocialite);
        } else {
            $userSocialiteRepository->method('getByUserProvider')->willThrowException(new ModelNotFoundException());
        }

        return new DeleteSocialiteService(
            $userSocialiteRepository,
            null,
            $this->getEventDispatcherMock($eventCount)
        );
    }
}
