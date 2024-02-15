<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use App\Model\User;
use OnixSystemsPHP\HyperfAuth\Guards\JwtGuard;
use OnixSystemsPHP\HyperfCore\Constants\Time;
use Qbhy\HyperfAuth\HyperfRedisCache;
use Qbhy\HyperfAuth\Provider\EloquentProvider;
use Qbhy\SimpleJwt\Encoders\Base64UrlSafeEncoder;
use Qbhy\SimpleJwt\EncryptAdapters\CryptEncrypter;
use Qbhy\SimpleJwt\EncryptAdapters\HS256Encrypter;
use Qbhy\SimpleJwt\EncryptAdapters\Md5Encrypter;
use Qbhy\SimpleJwt\EncryptAdapters\PasswordHashEncrypter;
use Qbhy\SimpleJwt\EncryptAdapters\SHA1Encrypter;

use function Hyperf\Support\env;
use function Hyperf\Support\make;

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
            'default' => HS256Encrypter::class,
            'drivers' => [
                PasswordHashEncrypter::alg() => PasswordHashEncrypter::class,
                CryptEncrypter::alg() => CryptEncrypter::class,
                SHA1Encrypter::alg() => SHA1Encrypter::class,
                Md5Encrypter::alg() => Md5Encrypter::class,
                HS256Encrypter::alg() => HS256Encrypter::class,
            ],
            'encoder' => new Base64UrlSafeEncoder(),
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
