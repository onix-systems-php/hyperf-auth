<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\HttpServer\Router\Router;
use OnixSystemsPHP\HyperfAuth\Controller\AuthController;
use OnixSystemsPHP\HyperfAuth\Controller\AuthSocialiteController;

Router::addGroup('/v1/auth', function () {
    Router::post('/login', [AuthController::class, 'login']);
    Router::post('/logout', [AuthController::class, 'logout']);
    Router::post('/refresh', [AuthController::class, 'refresh']);
});
Router::addGroup('/v1/auth-socialite', function () {
    Router::get('/{provider}/{app}/login', [AuthSocialiteController::class, 'getLoginLink']);
    Router::get('/{provider}/{app}/handler', [AuthSocialiteController::class, 'webHandler']);
    Router::post('/app/handler', [AuthSocialiteController::class, 'appHandler']);
    Router::delete('/{provider}', [AuthSocialiteController::class, 'deleteSocialProvider']);
});
