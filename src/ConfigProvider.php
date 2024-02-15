<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth;

use App\Repository\UserRepository;
use OnixSystemsPHP\HyperfAuth\Aspects\WsEventAuthAspect;
use OnixSystemsPHP\HyperfAuth\Contract\AuthenticatableRepository;
use OnixSystemsPHP\HyperfAuth\Contract\TokenGuardProvider;
use OnixSystemsPHP\HyperfAuth\Hashers\PasswordHashEncrypterFactory;
use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatableProvider;
use Qbhy\SimpleJwt\Interfaces\Encrypter;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                CoreAuthenticatableProvider::class => SessionManager::class,
                TokenGuardProvider::class => AuthManager::class,
                Encrypter::class => PasswordHashEncrypterFactory::class,
                AuthenticatableRepository::class => UserRepository::class,
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'aspects' => [
                WsEventAuthAspect::class,
            ],
            'publish' => [
                [
                    'id' => 'config_auth',
                    'description' => 'The auth config for onix-systems-php/hyperf-auth.',
                    'source' => __DIR__ . '/../publish/config/auth.php',
                    'destination' => BASE_PATH . '/config/autoload/auth.php',
                ],
                [
                    'id' => 'config_socialite',
                    'description' => 'The socialite config for onix-systems-php/hyperf-auth.',
                    'source' => __DIR__ . '/../publish/config/socialite.php',
                    'destination' => BASE_PATH . '/config/autoload/socialite.php',
                ],
                [
                    'id' => 'migration',
                    'description' => 'The socialite migration for onix-systems-php/hyperf-auth.',
                    'source' => __DIR__ . '/../publish/migrations/2022_05_13_090428_create_user_socialites_table.php',
                    'destination' => BASE_PATH . '/migrations/2022_05_13_090428_create_user_socialites_table.php',
                ],
            ],
        ];
    }
}
