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
                \Qbhy\SimpleJwt\Interfaces\Encrypter::class => \OnixSystemsPHP\HyperfAuth\Hashers\PasswordHashEncrypterFactory::class,
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
            'middlewares' => [
                'http' => [
                    \OnixSystemsPHP\HyperfAuth\Middleware\SessionMiddleware::class,
                ],
            ],
            'publish' => [
                [
                    'id' => 'config_auth',
                    'description' => 'The auth config for onix-systems-php/hyperf-auth.',
                    'source' => __DIR__ . '/../publish/config/auth.php',
                    'destination' => BASE_PATH . '/config/autoload/auth.php',
                ],
            ],
        ];
    }
}
