<?php

namespace OnixSystemsPHP\HyperfAuth\Socialite\Contracts;

interface Factory
{
    /**
     * Get an OAuth provider implementation.
     *
     * @param string|null $driver
     * @return \OnixSystemsPHP\HyperfAuth\Socialite\Contracts\Provider
     */
    public function driver(string|null $driver = null): Provider;

    /**
     * Make an OAuth provider implementation.
     *
     * @param string $provider
     * @param array $config
     * @return \OnixSystemsPHP\HyperfAuth\Socialite\Contracts\Provider
     */
    public function buildProvider(string $provider, array $config): Provider;
}
