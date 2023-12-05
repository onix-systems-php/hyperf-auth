<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Service;

use Hyperf\HttpServer\Response;
use OnixSystemsPHP\HyperfCore\Service\Service;

#[Service]
class GetSocialiteLoginLinkService
{
    public function __construct(
        private PrepareSocialiteProviderService $providerService,
    ) {}

    public function run(string $provider, ?string $app = null): string
    {
        $socialiteProvider = $this->providerService->run($provider, $app);
        /** @var Response $response */
        $response = $socialiteProvider->redirect();
        return $response->getHeaderLine('Location');
    }
}
