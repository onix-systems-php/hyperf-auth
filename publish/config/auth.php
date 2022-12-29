<?php

declare(strict_types=1);
use App\Model\User;
use OnixSystemsPHP\HyperfAuth\Guards\JwtGuard;
use OnixSystemsPHP\HyperfCore\Constants\Time;
use Qbhy\HyperfAuth\HyperfRedisCache;
use Qbhy\HyperfAuth\Provider\EloquentProvider;
use Qbhy\SimpleJwt\Encoders;
use Qbhy\SimpleJwt\EncryptAdapters as Encrypter;

return [
    'salt' => env('SALT'),
    'default' => [
        'guard' => 'jwt',
        'provider' => 'jwt-users',
    ],
    'apps' => [
        // app => [role, role]
        'user' => ['user'],
    ],
    'validators' => [
        'login' => 'required|email',
        'password' => 'required|min:6',
    ],
    'guards' => [
        'jwt' => [
            'driver' => JwtGuard::class,
            'provider' => 'jwt-users',
            'secret' => env('SIMPLE_JWT_SECRET', 'secret'),
            'header_name' => env('JWT_HEADER_NAME', 'Authorization'),
            'ttl' => (int) env('SIMPLE_JWT_TTL', Time::MINUTE * 2),
            'refresh_ttl' => (int) env('SIMPLE_JWT_REFRESH_TTL', Time::WEEK),
            'default' => Encrypter\HS256Encrypter::class,
            'drivers' => [
                Encrypter\PasswordHashEncrypter::alg() => Encrypter\PasswordHashEncrypter::class,
                Encrypter\CryptEncrypter::alg() => Encrypter\CryptEncrypter::class,
                Encrypter\SHA1Encrypter::alg() => Encrypter\SHA1Encrypter::class,
                Encrypter\Md5Encrypter::alg() => Encrypter\Md5Encrypter::class,
                Encrypter\HS256Encrypter::alg() => Encrypter\HS256Encrypter::class,
            ],
            'encoder' => new Encoders\Base64UrlSafeEncoder(),
            'cache' => function () {
                return make(HyperfRedisCache::class);
            },
            'prefix' => env('SIMPLE_JWT_PREFIX', 'default'),
        ],
    ],
    'providers' => [
        'jwt-users' => [
            'driver' => EloquentProvider::class,
            'model' => User::class,
        ],
    ],
];
