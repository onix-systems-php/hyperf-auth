<?php

declare(strict_types=1);
use Hyperf\HttpServer\Router\Router;

Router::addGroup('/v1/auth', function () {
    Router::post('/login', [\OnixSystemsPHP\HyperfAuth\Controller\AuthController::class, 'login']);
    Router::post('/logout', [\OnixSystemsPHP\HyperfAuth\Controller\AuthController::class, 'logout']);
    Router::post('/refresh', [\OnixSystemsPHP\HyperfAuth\Controller\AuthController::class, 'refresh']);
});
Router::addGroup('/v1/auth-socialite', function () {
    Router::get('/{provider}/{app}/login', [\OnixSystemsPHP\HyperfAuth\Controller\AuthSocialiteController::class, 'getLoginLink']);
    Router::get('/{provider}/{app}/handler', [\OnixSystemsPHP\HyperfAuth\Controller\AuthSocialiteController::class, 'webHandler']);
    Router::post('/app/handler', [\OnixSystemsPHP\HyperfAuth\Controller\AuthSocialiteController::class, 'appHandler']);
    Router::delete('/{provider}', [\OnixSystemsPHP\HyperfAuth\Controller\AuthSocialiteController::class, 'deleteSocialProvider']);
});
