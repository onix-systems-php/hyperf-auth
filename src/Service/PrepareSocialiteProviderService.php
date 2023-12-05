<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Service;

use Hyperf\Contract\ConfigInterface;
use OnixSystemsPHP\HyperfCore\Constants\ErrorCode;
use OnixSystemsPHP\HyperfCore\Exception\BusinessException;
use OnixSystemsPHP\HyperfCore\Service\Service;
use OnixSystemsPHP\HyperfSocialite\Facades\Socialite;
use OnixSystemsPHP\HyperfSocialite\Two\AbstractProvider;

use function Hyperf\Translation\__;

#[Service]
class PrepareSocialiteProviderService
{
    public function __construct(
        private ConfigInterface $config,
    ) {}

    public function run(string $provider, ?string $app = null): AbstractProvider
    {
        $providersConfig = $this->config->get('socialite', []);
        $providerAlias = $provider;

        if (! empty($app)) {
            $potentialAlias = $provider . '_' . $app;
            if (array_key_exists($potentialAlias, $providersConfig)) {
                $providerAlias = $potentialAlias;
            }
        }

        if (empty($providersConfig[$providerAlias])) {
            throw new BusinessException(ErrorCode::BAD_REQUEST_ERROR, __('exceptions.oauth.no_provider_config'));
        }

        $providerConfig = $providersConfig[$providerAlias];

        if (empty($providerConfig['provider'])) {
            return Socialite::with($providerAlias)->stateless();
        }

        return $this->createProvider($providerConfig);
    }

    private function createProvider(array $config): AbstractProvider
    {
        /** @var AbstractProvider $providerInstance */
        $providerInstance = Socialite::buildProvider($config['provider'], $config);
        $providerInstance->stateless();
        if (method_exists($providerInstance, 'setHost')) {
            $providerInstance->setHost($config['host'] ?? null);
        }

        return $providerInstance;
    }
}
