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

use Hyperf\Config\Config;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use OnixSystemsPHP\HyperfAuth\Contract\AuthenticatableRepository;
use OnixSystemsPHP\HyperfAuth\Contract\CreateSocialiteUserService;
use OnixSystemsPHP\HyperfAuth\DTO\AuthTokensDTO;
use OnixSystemsPHP\HyperfAuth\DTO\SocialiteHandlerDTO;
use OnixSystemsPHP\HyperfAuth\Guards\JwtGuard;
use OnixSystemsPHP\HyperfAuth\Model\UserSocialite;
use OnixSystemsPHP\HyperfAuth\Repository\UserSocialiteRepository;
use OnixSystemsPHP\HyperfAuth\Service\PrepareSocialiteProviderService;
use OnixSystemsPHP\HyperfAuth\Service\SocialiteHandlerService;
use OnixSystemsPHP\HyperfAuth\Test\Cases\AppTest;
use OnixSystemsPHP\HyperfAuth\Test\Mocks\AuthenticatableModel;
use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatableProvider;
use OnixSystemsPHP\HyperfCore\Exception\BusinessException;
use OnixSystemsPHP\HyperfSocialite\SocialiteManager;
use OnixSystemsPHP\HyperfSocialite\Two\GoogleProvider;
use OnixSystemsPHP\HyperfSocialite\Two\User;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @coversNothing
 */
class SocialiteHandlerServiceTest extends AppTest
{
    private Config $config;

    private User $user;

    private UserSocialite $userSocialite;

    public function setUp(): void
    {
        parent::setUp();
        $this->createContainer([['name' => 'get']]);
        $this->userSocialite = new UserSocialite([
            'id' => 1,
            'email' => 'test@test.com',
            'provider_id' => 21,
            'provider_name' => 'google',
            'user_id' => 2,
        ]);

        $this->setConfig();

        $this->user = new User();
        $this->user->name = 'FakeName';
        $this->user->email = 'mail@mail.m';
        $this->user->id = '1';
        $this->user->avatar = 'avatar';

        $this->trans = $this->createMock(TranslatorInterface::class);
        $this->trans->method('trans')->willReturn('fakeTrans');
    }

    public function testRegistration()
    {
        $this->createContainer([['name' => 'get']]);
        $this->setConfig();
        $service = $this->getService(2);
        $socialiteHandlerDTO = SocialiteHandlerDTO::make(['provider' => 'google', 'app' => 'user']);
        $jwtGuard = $this->createMock(JwtGuard::class);
        $request = $this->createMock(RequestInterface::class);
        $run = $service->run($socialiteHandlerDTO, $jwtGuard, $request);
        $this->assertInstanceOf(AuthTokensDTO::class, $run);
    }

    public function testConnect()
    {
        $this->createContainer([['name' => 'get']]);
        $this->setConfig();
        $service = $this->getService(0, true, userIsPresent: true);
        $socialiteHandlerDTO = SocialiteHandlerDTO::make(['provider' => 'google', 'app' => 'user']);
        $jwtGuard = $this->createMock(JwtGuard::class);
        $request = $this->createMock(RequestInterface::class);
        $run = $service->run($socialiteHandlerDTO, $jwtGuard, $request);
        $this->assertInstanceOf(AuthTokensDTO::class, $run);
    }

    public function testLogin()
    {
        $this->createContainer([['name' => 'get']]);
        $this->setConfig();
        $service = $this->getService(1, userIsPresent: true);
        $socialiteHandlerDTO = SocialiteHandlerDTO::make(['provider' => 'google', 'app' => 'user']);
        $jwtGuard = $this->createMock(JwtGuard::class);
        $request = $this->createMock(RequestInterface::class);
        $run = $service->run($socialiteHandlerDTO, $jwtGuard, $request);
        $this->assertInstanceOf(AuthTokensDTO::class, $run);
    }

    public function testIfEmptyRequest()
    {
        $this->setConfig();
        $this->createContainer([['name' => 'get', 'return' => $this->trans]]);
        $service = $this->getService();
        $socialiteHandlerDTO = SocialiteHandlerDTO::make(['provider' => 'google', 'app' => 'user']);
        $jwtGuard = $this->createMock(JwtGuard::class);
        $this->expectException(BusinessException::class);
        $service->run($socialiteHandlerDTO, $jwtGuard);
    }

    public function testIfCreateUserClassNotFound()
    {
        $this->createContainer([['name' => 'get', 'return' => $this->trans]]);
        $this->setConfig();
        $service = $this->getService(isCreateUserClassIsNotFound: true);
        $socialiteHandlerDTO = SocialiteHandlerDTO::make(['provider' => 'google', 'app' => 'user']);
        $jwtGuard = $this->createMock(JwtGuard::class);
        $request = $this->createMock(RequestInterface::class);
        $this->expectException(BusinessException::class);
        $service->run($socialiteHandlerDTO, $jwtGuard, $request);
    }

    public function testIfRoleEmptyInConfig()
    {
        $this->createContainer([['name' => 'get', 'return' => $this->trans]]);
        $this->setConfig();
        $this->config->set('socialite.apps.user', '');
        $service = $this->getService();
        $socialiteHandlerDTO = SocialiteHandlerDTO::make(['provider' => 'google', 'app' => 'user']);
        $jwtGuard = $this->createMock(JwtGuard::class);
        $request = $this->createMock(RequestInterface::class);
        $this->expectException(BusinessException::class);
        $service->run($socialiteHandlerDTO, $jwtGuard, $request);
    }

    public function testIfServicesEmptyInConfig()
    {
        $this->createContainer([['name' => 'get', 'return' => $this->trans]]);
        $this->setConfig();
        $this->config->set('socialite.services.create_user', '');
        $service = $this->getService();
        $socialiteHandlerDTO = SocialiteHandlerDTO::make(['provider' => 'google', 'app' => 'user']);
        $jwtGuard = $this->createMock(JwtGuard::class);
        $request = $this->createMock(RequestInterface::class);
        $this->expectException(BusinessException::class);
        $service->run($socialiteHandlerDTO, $jwtGuard, $request);
    }

    public function testIfAppEmptyInConfig()
    {
        $this->createContainer([['name' => 'get', 'return' => $this->trans]]);
        $this->setConfig();
        $this->config->set('auth.apps.user', ['fakeData']);
        $service = $this->getService(1);
        $socialiteHandlerDTO = SocialiteHandlerDTO::make(['provider' => 'google', 'app' => 'user']);
        $jwtGuard = $this->createMock(JwtGuard::class);
        $request = $this->createMock(RequestInterface::class);
        $this->expectException(BusinessException::class);
        $service->run($socialiteHandlerDTO, $jwtGuard, $request);
    }

    protected function getService(
        int $eventCount = 0,
        bool $is_session = false,
        bool $userIsPresent = false,
        bool $isCreateUserClassIsNotFound = false,
    ): SocialiteHandlerService {
        $googleProvider = $this->createMock(GoogleProvider::class);
        $socialiteManager = $this->createMock(SocialiteManager::class);
        $socialiteManager->method('with')->willReturn($googleProvider);

        $googleProvider = $this->createMock(GoogleProvider::class);
        $googleProvider->method('user')->willReturn($this->user);

        $authenticatable = $this->createMock(AuthenticatableModel::class);
        $authenticatable->method('getRole')->willReturn('user');

        $this->userSocialite->user = $authenticatable;

        $userSocialiteRepository = $this->createMock(UserSocialiteRepository::class);
        if ($userIsPresent) {
            $userSocialiteRepository->method('getByProviderData')->willReturn($this->userSocialite);
        }

        $prepareSocialiteProviderService = $this->createMock(PrepareSocialiteProviderService::class);
        $prepareSocialiteProviderService->method('run')->willReturn($googleProvider);

        $coreAuthenticatableProvider = $this->createMock(CoreAuthenticatableProvider::class);
        if ($is_session) {
            $coreAuthenticatableProvider->method('user')->willReturn($authenticatable);
        }

        $authenticatableRepository = $this->createMock(AuthenticatableRepository::class);
        $containerInterface = $this->createMock(ContainerInterface::class);

        $createSocialiteUserService = $this->createMock(CreateSocialiteUserService::class);

        $createSocialiteUserService->method('run')->willReturn($authenticatable);
        if (! $isCreateUserClassIsNotFound) {
            $containerInterface->method('get')->willReturn($createSocialiteUserService);
        }

        return new SocialiteHandlerService(
            $userSocialiteRepository,
            $prepareSocialiteProviderService,
            $coreAuthenticatableProvider,
            $authenticatableRepository,
            $this->getEventDispatcherMock($eventCount),
            $this->config,
            $containerInterface,
            null
        );
    }

    private function setConfig(): void
    {
        $this->config = new Config([]);
        $this->config->set('socialite.apps.user', 'user');
        $this->config->set('auth.apps.user', ['user']);
        $this->config->set('socialite.services.create_user', '\Fake::class');
    }
}
