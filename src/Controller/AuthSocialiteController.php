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
use OpenApi\Attributes as OA;

class AuthSocialiteController extends AbstractController
{
    public function __construct(
        private TokenGuardProvider $authManager,
        private CoreAuthenticatableProvider $authenticatableProvider,
    ) {
    }


    #[OA\Get(
        path: '/v1/auth-socialite/{provider}/{app}/login',
        operationId: 'redirectLoginAuthSocialite',
        summary: 'Generate redirect url',
        tags: ['auth_socialite'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/Locale'),
            new OA\Parameter(name: 'provider', in: 'path', required: true, schema: new OA\Schema(type: 'string'), description: 'Social provider'),
            new OA\Parameter(name: 'app', in: 'path', required: true, schema: new OA\Schema(type: 'string'), description: 'Requesting app keyname'),
        ],
        responses: [
            new OA\Response(response: 200, description: '', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'data', ref: '#/components/schemas/ResourceLoginLink'),
            ])),
            new OA\Response(response: 401, ref: '#/components/responses/401'),
            new OA\Response(response: 422, ref: '#/components/responses/422'),
            new OA\Response(response: 500, ref: '#/components/responses/500'),
        ],
    )]
    public function getLoginLink(RequestProviders $request, GetSocialiteLoginLinkService $redirectService): ResourceLoginLink
    {
        return new ResourceLoginLink($redirectService->run($request->all()['provider'], $request->all()['app']));
    }

    #[OA\Get(
        path: '/v1/auth-socialite/{provider}/{app}/handler',
        operationId: 'webHandlerAuthSocialite',
        summary: 'Login from web.',
        tags: ['auth_socialite'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/Locale'),
            new OA\Parameter(name: 'provider', in: 'path', required: true, schema: new OA\Schema(type: 'string'), description: 'Social provider'),
            new OA\Parameter(name: 'app', in: 'path', required: true, schema: new OA\Schema(type: 'string'), description: 'Requesting app keyname'),
            new OA\Parameter(name: 'code', in: 'query', schema: new OA\Schema(type: 'string'), description: 'OAuth code'),
        ],
        responses: [
            new OA\Response(response: 200, description: '', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'data', ref: '#/components/schemas/ResourceAuthToken'),
            ])),
            new OA\Response(response: 401, ref: '#/components/responses/404'),
            new OA\Response(response: 422, ref: '#/components/responses/422'),
            new OA\Response(response: 500, ref: '#/components/responses/500'),
        ],
    )]
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

    #[OA\Post(
        path: '/v1/auth-socialite/app/handler',
        operationId: 'appHandlerAuthSocialite',
        summary: 'Login from app.',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/RequestAppHandler')),
        tags: ['auth_socialite'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/Locale'),
        ],
        responses: [
            new OA\Response(response: 200, description: '', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'data', ref: '#/components/schemas/ResourceAuthToken'),
            ])),
            new OA\Response(response: 401, ref: '#/components/responses/404'),
            new OA\Response(response: 422, ref: '#/components/responses/422'),
            new OA\Response(response: 500, ref: '#/components/responses/500'),
        ],
    )]
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

    #[OA\Delete(
        path: '/v1/auth-socialite/{provider}',
        operationId: 'deleteSocialConnection',
        summary: 'Delete social connection by provider.',
        security: [['bearerAuth' => []]],
        tags: ['auth_socialite'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/Locale'),
            new OA\Parameter(name: 'provider', in: 'path', required: true, schema: new OA\Schema(type: 'string'), description: 'Social provider'),
        ],
        responses: [
            new OA\Response(response: 200, description: '', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'status', type: 'string'),
                new OA\Property(property: 'data', ref: '#/components/schemas/ResourceSuccess'),
            ])),
            new OA\Response(response: 401, ref: '#/components/responses/404'),
            new OA\Response(response: 500, ref: '#/components/responses/500'),
        ],
    )]
    public function deleteSocialProvider(
        string $provider,
        DeleteSocialiteService $service
    ): ResourceSuccess {
        $service->run($provider, $this->authenticatableProvider->user()->getId());
        return ResourceSuccess::make([]);
    }
}
