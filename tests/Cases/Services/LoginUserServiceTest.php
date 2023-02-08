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

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Validation\UnauthorizedException;
use OnixSystemsPHP\HyperfAuth\Contract\Authenticatable;
use OnixSystemsPHP\HyperfAuth\Contract\AuthenticatableRepository;
use OnixSystemsPHP\HyperfAuth\Contract\TokenGuard;
use OnixSystemsPHP\HyperfAuth\DTO\LoginDTO;
use OnixSystemsPHP\HyperfAuth\Service\LoginUserService;
use OnixSystemsPHP\HyperfAuth\Test\Cases\AppTest;
use OnixSystemsPHP\HyperfCore\Exception\BusinessException;
use Qbhy\SimpleJwt\Interfaces\Encrypter;

/**
 * @internal
 * @coversNothing
 */
class LoginUserServiceTest extends AppTest
{
    public function setUp(): void
    {
        $trans = $this->createMock(TranslatorInterface::class);
        $trans->method('trans')->willReturn('fakeTrans');
        $this->createContainer([['name' => 'get', 'return' => $trans]]);
        parent::setUp();
    }

    public function testMain(): void
    {
        $loginDto = LoginDTO::make(['login' => 'Login', 'password' => '123', 'app' => 'admin']);
        $tokenGuard = $this->createMock(TokenGuard::class);
        $service = $this->getService('admin', true, ['user', 'admin'], 1);
        $service->run($loginDto, $tokenGuard);
        $this->assertTrue(true);
    }

    public function testIfUserIsEmpty(): void
    {
        $loginDto = LoginDTO::make(['login' => 'Login', 'password' => '123', 'app' => 'admin']);
        $tokenGuard = $this->createMock(TokenGuard::class);
        $service = $this->getService(null, true, ['user', 'admin'], 0);
        $this->expectException(BusinessException::class);
        $service->run($loginDto, $tokenGuard);
    }

    public function testIfRoleIsNotPresentInConfig(): void
    {
        $loginDto = LoginDTO::make(['login' => 'Login', 'password' => '123', 'app' => 'admin']);
        $tokenGuard = $this->createMock(TokenGuard::class);
        $service = $this->getService('admin', true, [], 0);
        $this->expectException(BusinessException::class);
        $service->run($loginDto, $tokenGuard);
    }

    public function testIfEncrypterCheckFalse(): void
    {
        $loginDto = LoginDTO::make(['login' => 'Login', 'password' => '123', 'app' => 'admin']);
        $tokenGuard = $this->createMock(TokenGuard::class);
        $service = $this->getService('admin', false, ['user', 'admin'], 0);
        $this->expectException(BusinessException::class);
        $service->run($loginDto, $tokenGuard);
    }

    public function testIfNotValidData(): void
    {
        $loginDto = LoginDTO::make(['login' => 'Login', 'password' => '', 'app' => 'admin']);
        $tokenGuard = $this->createMock(TokenGuard::class);
        $service = $this->getService('admin', true, ['user', 'admin'], 0, false);
        $this->expectException(UnauthorizedException::class);
        $service->run($loginDto, $tokenGuard);
    }

    protected function getService(
        ?string $role,
        bool $check,
        string|array $config,
        int $eventCount,
        bool $is_valid = true,
    ): LoginUserService {
        $validatorFactoryInterface = $this->createMock(ValidatorFactoryInterface::class);
        if (! $is_valid) {
            $validatorFactoryInterface->method('make')->willThrowException(new UnauthorizedException());
        }

        $encrypter = $this->createMock(Encrypter::class);
        $encrypter->method('check')->willReturn($check);

        $authenticatableRepository = $this->createMock(AuthenticatableRepository::class);
        if ($role) {
            $authenticatable = $this->createMock(Authenticatable::class);
            $authenticatable->method('getRole')->willReturn($role);
            $authenticatableRepository->method('getByLogin')->willReturn($authenticatable);

        }
        $configInterface = $this->createMock(ConfigInterface::class);
        $configInterface->method('get')->willReturn($config);

        return new LoginUserService(
            $validatorFactoryInterface,
            $encrypter,
            $authenticatableRepository,
            $this->getEventDispatcherMock($eventCount),
            $configInterface,
            null
        );
    }
}
