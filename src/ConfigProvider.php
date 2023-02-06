<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth;

class ConfigProvider
{
    public function __invoke(): array
    {

        return [
            'dependencies' => [
                \OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatableProvider::class => \OnixSystemsPHP\HyperfAuth\SessionManager::class,
                \OnixSystemsPHP\HyperfAuth\Contract\TokenGuardProvider::class => \OnixSystemsPHP\HyperfAuth\AuthManager::class,
                \Qbhy\SimpleJwt\Interfaces\Encrypter::class => \OnixSystemsPHP\HyperfAuth\Hashers\PasswordHashEncrypterFactory::class,
                \OnixSystemsPHP\HyperfAuth\Contract\AuthenticatableRepository::class => \App\Repository\UserRepository::class,
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
                \OnixSystemsPHP\HyperfAuth\Aspects\WsEventAuthAspect::class,
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
