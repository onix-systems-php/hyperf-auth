<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Hashers;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use Qbhy\SimpleJwt\EncryptAdapters\PasswordHashEncrypter;

class PasswordHashEncrypterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $secret = $config->get('auth.salt', false);
        return make(PasswordHashEncrypter::class, compact('secret'));
    }
}
