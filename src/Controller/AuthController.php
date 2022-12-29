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
use OpenApi\Annotations as OA;

class AuthController extends AbstractController
{
    public function __construct(
        private AuthManager $authManager,
    ) {
    }

    /**
     * @OA\Post(
     *     path="/v1/auth/login",
     *     summary="Login user",
     *     operationId="login",
     *     tags={"auth"},
     *     @OA\Parameter(ref="#/components/parameters/Locale"),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/RequestLogin")),
     *     @OA\Response(response=200, description="", @OA\JsonContent(
     *         @OA\Property(property="status", type="string"),
     *         @OA\Property(property="data", ref="#/components/schemas/ResourceAuthToken"),
     *     )),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=422, ref="#/components/responses/422"),
     *     @OA\Response(response=500, ref="#/components/responses/500"),
     * )
     */
    public function login(RequestLogin $request, LoginUserService $loginUserService): ResourceAuthToken
    {
        $tokens = $loginUserService->run(LoginDTO::make($request), $this->authManager->tokenGuard());
        return new ResourceAuthToken($tokens);
    }

    /**
     * @OA\Post(
     *     path="/v1/auth/logout",
     *     summary="Logout user",
     *     operationId="logout",
     *     tags={"auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/Locale"),
     *     @OA\Response(response=200, description="", @OA\JsonContent(
     *         @OA\Property(property="status", type="string"),
     *         @OA\Property(property="data", ref="#/components/schemas/ResourceSuccess"),
     *     )),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=500, ref="#/components/responses/500"),
     * )
     */
    public function logout(
        LogoutUserService $logoutUserService
    ): ResourceSuccess {
        $logoutUserService->run($this->authManager);
        return new ResourceSuccess([]);
    }

    /**
     * @OA\Post(
     *     path="/v1/auth/refresh",
     *     summary="Refresh Auth token",
     *     operationId="refresh",
     *     tags={"auth"},
     *     @OA\Parameter(ref="#/components/parameters/Locale"),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/RequestRefresh")),
     *     @OA\Response(response=200, description="", @OA\JsonContent(
     *         @OA\Property(property="status", type="string"),
     *         @OA\Property(property="data", ref="#/components/schemas/ResourceAuthToken"),
     *     )),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=500, ref="#/components/responses/500"),
     * )
     */
    public function refresh(
        RefreshTokenService $refreshTokenService
    ): ResourceAuthToken {
        $tokens = $refreshTokenService->run($this->authManager->tokenGuard());
        return new ResourceAuthToken($tokens);
    }
}
