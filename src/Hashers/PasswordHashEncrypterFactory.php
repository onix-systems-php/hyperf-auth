<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Hashers;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use Qbhy\SimpleJwt\EncryptAdapters\PasswordHashEncrypter;

use function Hyperf\Support\make;

class PasswordHashEncrypterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $secret = $config->get('auth.salt', false);
        return make(PasswordHashEncrypter::class, compact('secret'));
    }
}
