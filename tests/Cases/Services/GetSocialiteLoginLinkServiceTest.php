<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Test\Cases\Services;

use OnixSystemsPHP\HyperfAuth\Service\GetSocialiteLoginLinkService;
use OnixSystemsPHP\HyperfAuth\Service\PrepareSocialiteProviderService;
use OnixSystemsPHP\HyperfAuth\Test\Cases\AppTest;
use OnixSystemsPHP\HyperfSocialite\Two\AbstractProvider;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * @internal
 * @coversNothing
 */
class GetSocialiteLoginLinkServiceTest extends AppTest
{
    public function testMain()
    {
        $exceptResponse = 'http://fake-url';
        $service = $this->getService($exceptResponse);
        $response = $service->run('google', 'user');
        $this->assertEquals($exceptResponse, $response);

        $exceptResponse = 'http://fake-url';
        $service = $this->getService($exceptResponse);
        $response = $service->run('fakeProvider');
        $this->assertEquals($exceptResponse, $response);
    }

    protected function getService(string $response): GetSocialiteLoginLinkService
    {
        $psrResponseInterface = $this->createMock(PsrResponseInterface::class);
        $psrResponseInterface->method('getHeaderLine')->willReturn($response);

        $abstractProvider = $this->createMock(AbstractProvider::class);
        $abstractProvider->method('redirect')->willReturn($psrResponseInterface);

        $prepareSocialiteProviderService = $this->createMock(PrepareSocialiteProviderService::class);
        $prepareSocialiteProviderService->method('run')->willReturn($abstractProvider);

        return new GetSocialiteLoginLinkService(
            $prepareSocialiteProviderService
        );
    }
}
