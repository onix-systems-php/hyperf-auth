<?php

namespace OnixSystemsPHP\HyperfAuth\Test\Cases\Services;

use Hyperf\Contract\ConfigInterface;
use OnixSystemsPHP\HyperfAuth\Service\PrepareSocialiteProviderService;
use OnixSystemsPHP\HyperfAuth\Test\Cases\AppTest;
use OnixSystemsPHP\HyperfSocialite\SocialiteManager;
use OnixSystemsPHP\HyperfSocialite\Two\GoogleProvider;
use PHPUnit\Framework\Error\Warning;

class PrepareSocialiteProviderServiceTest extends AppTest
{
    public function setUp(): void
    {
        $googleProvider = $this->createMock(GoogleProvider::class);
        $socialiteManager = $this->createMock(SocialiteManager::class);
        $socialiteManager->method('with')->willReturn($googleProvider);
        $this->createContainer([['name' => 'make', 'return' => $socialiteManager]]);

        parent::setUp();
    }

    public function testMain()
    {
        $service = $this->getService(['google' => 'ok']);
        $service->run('google', 'user');
        $this->assertTrue(true);
    }

    public function testProviderApp()
    {
        $service = $this->getService(['google_user' => 'ok']);
        $service->run('google', 'user');
        $this->assertTrue(true);
    }

    public function testConfigIsEmpty()
    {
        $service = $this->getService([]);
        try {
            $service->run('google', 'user');
            $this->fail();
        } catch (Warning) {
            $this->assertTrue(true);
        }
    }

    public function testProviderProviderPresentInConfig()
    {
        $service = $this->getService(['google' => ['provider' => 'google']]);
        $provider = $service->run('google', 'user');
        $this->assertTrue(true);
    }

    protected function getService(array $config): PrepareSocialiteProviderService
    {
        $configInterface = $this->createMock(ConfigInterface::class);
        $configInterface->method('get')->willReturn($config);

        return new PrepareSocialiteProviderService(
            $configInterface
        );
    }
}
