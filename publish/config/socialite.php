<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use App\Resource\User\ResourceUser;

use function Hyperf\Support\env;

return [
    'active_providers' => env('SOCIAL_ACTIVE_PROVIDERS', implode(',', ['google', 'github', 'gitlab', 'facebook', 'linkedin', 'bitbucket'])),
    'apps' => [
        // app => default role
        'user' => 'user',
    ],
    'user_resource' => ResourceUser::class,
    'services' => [
        'create_user' => null,
        'assign_avatar' => null,
    ],
    'github' => [
        'client_id' => env('SOCIAL_GITHUB_CLIENT_ID', ''),
        'client_secret' => env('SOCIAL_GITHUB_CLIENT_SECRET', ''),
        'redirect' => env('SOCIAL_GITHUB_REDIRECT_URL', ''),
    ],
    'facebook' => [
        'client_id' => env('SOCIAL_FACEBOOK_CLIENT_ID', ''),
        'client_secret' => env('SOCIAL_FACEBOOK_CLIENT_SECRET', ''),
        'redirect' => env('SOCIAL_FACEBOOK_REDIRECT_URL', ''),
    ],
    'google' => [
        'client_id' => env('SOCIAL_GOOGLE_CLIENT_ID', ''),
        'client_secret' => env('SOCIAL_GOOGLE_CLIENT_SECRET', ''),
        'redirect' => env('SOCIAL_GOOGLE_REDIRECT_URL', ''),
    ],
    'linkedin' => [
        'client_id' => env('SOCIAL_LINKEDIN_CLIENT_ID', ''),
        'client_secret' => env('SOCIAL_LINKEDIN_CLIENT_SECRET', ''),
        'redirect' => env('SOCIAL_LINKEDIN_REDIRECT_URL', ''),
    ],
    'bitbucket' => [
        'client_id' => env('SOCIAL_BITBUCKET_CLIENT_ID', ''),
        'client_secret' => env('SOCIAL_BITBUCKET_CLIENT_SECRET', ''),
        'redirect' => env('SOCIAL_BITBUCKET_REDIRECT_URL', ''),
    ],
    'gitlab' => [
        'client_id' => env('SOCIAL_GITLAB_CLIENT_ID', ''),
        'client_secret' => env('SOCIAL_GITLAB_CLIENT_SECRET', ''),
        'redirect' => env('SOCIAL_GITLAB_REDIRECT_URL', ''),
        'host' => env('SOCIAL_GITLAB_HOST', ''),
    ],
    'logger' => [
        'name' => 'socialite',
        'group' => 'default',
    ],
];
