<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Controller;

use OnixSystemsPHP\HyperfAuth\AuthManager;
use OnixSystemsPHP\HyperfAuth\DTO\LoginDTO;
use OnixSystemsPHP\HyperfAuth\Request\RequestLogin;
use OnixSystemsPHP\HyperfAuth\Resource\ResourceAuthToken;
use OnixSystemsPHP\HyperfAuth\Service\LoginUserService;
use OnixSystemsPHP\HyperfAuth\Service\LogoutUserService;
use OnixSystemsPHP\HyperfAuth\Service\RefreshTokenService;
use OnixSystemsPHP\HyperfCore\Controller\AbstractController;
use OnixSystemsPHP\HyperfCore\Resource\ResourceSuccess;
use OpenApi\Attributes as OA;

class AuthController extends AbstractController
{
    public function __construct(
        private AuthManager $authManager,
    ) {
    }

    #[OA\Post(
        path: '/v1/auth/login',
        operationId: 'login',
        summary: 'Login user',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RequestLogin')),
        tags: ['auth'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/Locale'),
        ],
        responses: [
            new OA\Response(response: 200, description: '', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'data', ref: '#/components/schemas/ResourceAuthToken'),
            ])),
            new OA\Response(response: 404, ref: '#/components/responses/404'),
            new OA\Response(response: 422, ref: '#/components/responses/422'),
            new OA\Response(response: 500, ref: '#/components/responses/500'),
        ],
    )
    public function login(RequestLogin $request, LoginUserService $loginUserService): ResourceAuthToken
    {
        $tokens = $loginUserService->run(LoginDTO::make($request), $this->authManager->tokenGuard());
        return new ResourceAuthToken($tokens);
    }

    #[OA\Post(
        path: '/v1/auth/logout',
        operationId: 'logout',
        summary: 'Logout user',
        security: [['bearerAuth' => []]],
        tags: ['auth'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/Locale'),
        ],
        responses: [
            new OA\Response(response: 200, description: '', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'data', ref: '#/components/schemas/ResourceSuccess'),
            ])),
            new OA\Response(response: 401, ref: '#/components/responses/401'),
            new OA\Response(response: 500, ref: '#/components/responses/500'),
        ],
    )]
    public function logout(
        LogoutUserService $logoutUserService
    ): ResourceSuccess {
        $logoutUserService->run($this->authManager);
        return new ResourceSuccess([]);
    }


    #[OA\Post(
        path: '/v1/auth/refresh',
        operationId: 'refresh',
        summary: 'Refresh Auth token',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RequestRefresh')),
        tags: ['auth'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/Locale'),
        ],
        responses: [
            new OA\Response(response: 200, description: '', content: new OA\JsonContent(parameters: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'data', ref: '#/components/schemas/ResourceAuthToken'),
            ])),
            new OA\Response(response: 401, ref: '#/components/responses/401'),
            new OA\Response(response: 500, ref: '#/components/responses/500'),
        ],
    )]
    public function refresh(
        RefreshTokenService $refreshTokenService
    ): ResourceAuthToken {
        $tokens = $refreshTokenService->run($this->authManager->tokenGuard());
        return new ResourceAuthToken($tokens);
    }
}
