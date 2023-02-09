<?php

namespace OnixSystemsPHP\HyperfAuth\Test\Cases\Services;

use Hyperf\Config\Config;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\SessionInterface;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Request;
use OnixSystemsPHP\HyperfAuth\Service\PrepareSocialiteProviderService;
use OnixSystemsPHP\HyperfAuth\Test\Cases\AppTest;
use OnixSystemsPHP\HyperfCore\Exception\BusinessException;
use OnixSystemsPHP\HyperfSocialite\Contracts\Factory;
use OnixSystemsPHP\HyperfSocialite\SocialiteManager;
use OnixSystemsPHP\HyperfSocialite\Two\GithubProvider;
use OnixSystemsPHP\HyperfSocialite\Two\GoogleProvider;

class PrepareSocialiteProviderServiceTest extends AppTest
{
    private ConfigInterface $config;

    public function setUp(): void
    {
        $trans = $this->createMock(TranslatorInterface::class);
        $trans->method('trans')->willReturn('fakeTrans');
        $this->setConfig();
        $this->createContainer([
            TranslatorInterface::class => $trans,
            ConfigInterface::class => $this->config,
            RequestInterface::class => new Request(),
            SessionInterface::class => $this->createMock(SessionInterface::class),
        ]);

        $this->container->set(Factory::class, new SocialiteManager($this->container));

        parent::setUp();
    }

    public function testGithub()
    {
        $this->setConfig();
        $service = new PrepareSocialiteProviderService($this->config);
        $provider = $service->run('github', 'user');
        $this->assertInstanceOf(GithubProvider::class, $provider);
    }

    public function testGoogleUser()
    {
        $this->setConfig();
        $service = new PrepareSocialiteProviderService($this->config);
        $provider = $service->run('google_user', 'user');
        $this->assertInstanceOf(GoogleProvider::class, $provider);
    }

    public function testConfigIsEmpty()
    {
        $this->config = new Config([]);
        $service = new PrepareSocialiteProviderService($this->config);
        try {
            $service->run('google', 'user');
            $this->fail();
        } catch (BusinessException) {
            $this->assertTrue(true);
        }
    }

    public function testProviderProviderPresentInConfig()
    {
        $this->setConfig();
        $service = new PrepareSocialiteProviderService($this->config);
        $provider = $service->run('google', 'user');
        $this->assertInstanceOf(GoogleProvider::class, $provider);
    }

    private function setConfig(): void
    {
        $this->config = new Config([]);
        $providerData = ['client_id' => 'fakeId', 'client_secret' => 'fakeSecret', 'redirect' => 'https://fake-url'];
        $this->config->set('socialite.github', $providerData);
        $this->config->set('socialite.google_user', ['provider' => GoogleProvider::class, ...$providerData]);
    }
}
