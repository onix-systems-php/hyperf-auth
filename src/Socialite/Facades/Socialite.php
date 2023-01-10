<?php

namespace OnixSystemsPHP\HyperfAuth\Socialite\Facades;

use OnixSystemsPHP\HyperfAuth\Socialite\Contracts\Factory;

/**
 * @method static \OnixSystemsPHP\HyperfAuth\Socialite\Contracts\Provider driver(string $driver = null)
 * @method static \OnixSystemsPHP\HyperfAuth\Socialite\Contracts\Provider with(string $driver = null)
 * @method static \OnixSystemsPHP\HyperfAuth\Socialite\Contracts\Provider buildProvider(string $provider, array $config)
 * @see \OnixSystemsPHP\HyperfAuth\Socialite\SocialiteManager
 */
class Socialite
{
    protected Factory $manager;

    public function __construct()
    {
        $this->manager = make(Factory::class);
    }

    public function __call($name, $arguments)
    {
        return call([$this->manager, $name], $arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        return (new static())->{$name}(...$arguments);
    }
}
