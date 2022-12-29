<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Controller;

use OnixSystemsPHP\HyperfAuth\Contract\TokenGuardProvider;
use OnixSystemsPHP\HyperfAuth\DTO\SocialiteHandlerDTO;
use OnixSystemsPHP\HyperfAuth\Request\RequestAppHandler;
use OnixSystemsPHP\HyperfAuth\Request\RequestProviders;
use OnixSystemsPHP\HyperfAuth\Resource\ResourceAuthToken;
use OnixSystemsPHP\HyperfAuth\Resource\ResourceLoginLink;
use OnixSystemsPHP\HyperfAuth\Service\DeleteSocialiteService;
use OnixSystemsPHP\HyperfAuth\Service\GetSocialiteLoginLinkService;
use OnixSystemsPHP\HyperfAuth\Service\SocialiteHandlerService;
use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatableProvider;
use OnixSystemsPHP\HyperfCore\Controller\AbstractController;
use OnixSystemsPHP\HyperfCore\Resource\ResourceSuccess;
use OpenApi\Annotations as OA;

class AuthSocialiteController extends AbstractController
{
    public function __construct(
        private TokenGuardProvider $authManager,
        private CoreAuthenticatableProvider $authenticatableProvider,
    ) {
    }

    /**
     * @OA\Get(
     *     path="/v1/auth-socialite/{provider}/{app}/login",
     *     summary="Generate redirect url",
     *     operationId="redirectLoginAuthSocialite",
     *     tags={"auth_socialite"},
     *     @OA\Parameter(ref="#/components/parameters/Locale"),
     *     @OA\Parameter(name="provider", in="path", required=true, @OA\Schema(type="string"), description="Social provider"),
     *     @OA\Parameter(name="app", in="path", required=true, @OA\Schema(type="string"), description="Requesting app keyname"),
     *     @OA\Response(response=200, description="", @OA\JsonContent(
     *         @OA\Property(property="status", type="string"),
     *         @OA\Property(property="data", ref="#/components/schemas/ResourceLoginLink"),
     *     )),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=422, ref="#/components/responses/422"),
     *     @OA\Response(response=500, ref="#/components/responses/500"),
     * )
     */
    public function getLoginLink(RequestProviders $request, GetSocialiteLoginLinkService $redirectService): ResourceLoginLink
    {
        return new ResourceLoginLink($redirectService->run($request->all()['provider'], $request->all()['app']));
    }

    /**
     * @OA\Get(
     *     path="/v1/auth-socialite/{provider}/{app}/handler",
     *     summary="Login from web.",
     *     operationId="webHandlerAuthSocialite",
     *     tags={"auth_socialite"},
     *     @OA\Parameter(ref="#/components/parameters/Locale"),
     *     @OA\Parameter(name="provider", in="path", required=true, @OA\Schema(type="string"), description="Social provider"),
     *     @OA\Parameter(name="app", in="path", required=true, @OA\Schema(type="string"), description="Requesting app keyname"),
     *     @OA\Parameter(name="code", in="query", @OA\Schema(type="string"), description="OAuth code"),
     *     @OA\Response(response=200, description="", @OA\JsonContent(
     *         @OA\Property(property="status", type="string"),
     *         @OA\Property(property="data", ref="#/components/schemas/ResourceAuthToken"),
     *     )),
     *     @OA\Response(response=401, ref="#/components/responses/404"),
     *     @OA\Response(response=422, ref="#/components/responses/422"),
     *     @OA\Response(response=500, ref="#/components/responses/500"),
     * )
     */
    public function webHandler(
        RequestProviders $request,
        SocialiteHandlerService $handlerService
    ): ResourceAuthToken {
        $socialiteHandlerDTO = SocialiteHandlerDTO::make([
            'provider' => $request->all()['provider'],
            'app' => $request->all()['app'],
            'token' => '',
        ]);
        $tokens = $handlerService->run(
            $socialiteHandlerDTO,
            $this->authManager->tokenGuard(),
            $request,
        );
        return ResourceAuthToken::make($tokens);
    }

    /**
     * @OA\Post(
     *     path="/v1/auth-socialite/app/handler",
     *     summary="Login from app.",
     *     operationId="appHandlerAuthSocialite",
     *     tags={"auth_socialite"},
     *     @OA\Parameter(ref="#/components/parameters/Locale"),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/RequestAppHandler")),
     *     @OA\Response(response=200, description="", @OA\JsonContent(
     *         @OA\Property(property="status", type="string"),
     *         @OA\Property(property="data", ref="#/components/schemas/ResourceAuthToken"),
     *     )),
     *     @OA\Response(response=401, ref="#/components/responses/404"),
     *     @OA\Response(response=422, ref="#/components/responses/422"),
     *     @OA\Response(response=500, ref="#/components/responses/500"),
     * )
     */
    public function appHandler(
        RequestAppHandler $request,
        SocialiteHandlerService $handlerService
    ): ResourceAuthToken {
        $tokens = $handlerService->run(
            SocialiteHandlerDTO::make($request),
            $this->authManager->tokenGuard()
        );
        return ResourceAuthToken::make($tokens);
    }

    /**
     * @OA\Delete(
     *     path="/v1/auth-socialite/{provider}",
     *     summary="Delete social connection by provider.",
     *     operationId="deleteSocialConnection",
     *     tags={"auth_socialite"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/Locale"),
     *     @OA\Parameter(name="provider", in="path", required=true, @OA\Schema(type="string"), description="Social provider"),
     *     @OA\Response(response=200, description="", @OA\JsonContent(
     *         @OA\Property(property="status", type="string"),
     *         @OA\Property(property="data", ref="#/components/schemas/ResourceSuccess"),
     *     )),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=500, ref="#/components/responses/500"),
     * )
     */
    public function deleteSocialProvider(
        string $provider,
        DeleteSocialiteService $service
    ): ResourceSuccess {
        $service->run($provider, $this->authenticatableProvider->user()->getId());
        return ResourceSuccess::make([]);
    }
}
