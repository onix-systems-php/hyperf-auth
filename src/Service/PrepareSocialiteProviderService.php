<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Service;

use Hyperf\Contract\ConfigInterface;
use OnixSystemsPHP\HyperfCore\Service\Service;
use Xtwoend\HySocialite\Facades\Socialite;
use Xtwoend\HySocialite\Two\AbstractProvider;

#[Service]
class PrepareSocialiteProviderService
{
    public function __construct(
        private ConfigInterface $config,
    ) {
    }

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
