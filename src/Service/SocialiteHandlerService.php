<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Service;

use Hyperf\Contract\ConfigInterface;
use Hyperf\DbConnection\Annotation\Transactional;
use Hyperf\HttpServer\Contract\RequestInterface;
use OnixSystemsPHP\HyperfActionsLog\Event\Action;
use OnixSystemsPHP\HyperfAuth\Contract\AssignSocialiteAvatarService;
use OnixSystemsPHP\HyperfAuth\Contract\Authenticatable;
use OnixSystemsPHP\HyperfAuth\Contract\AuthenticatableRepository;
use OnixSystemsPHP\HyperfAuth\Contract\CreateSocialiteUserService;
use OnixSystemsPHP\HyperfAuth\DTO\AuthTokensDTO;
use OnixSystemsPHP\HyperfAuth\DTO\SocialiteHandlerDTO;
use OnixSystemsPHP\HyperfAuth\DTO\UserSocialiteDTO;
use OnixSystemsPHP\HyperfAuth\Guards\JwtGuard;
use OnixSystemsPHP\HyperfAuth\Repository\UserSocialiteRepository;
use OnixSystemsPHP\HyperfCore\Constants\ErrorCode;
use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatableProvider;
use OnixSystemsPHP\HyperfCore\Contract\CorePolicyGuard;
use OnixSystemsPHP\HyperfCore\Exception\BusinessException;
use OnixSystemsPHP\HyperfCore\Service\Service;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Xtwoend\HySocialite\AbstractUser;

#[Service]
class SocialiteHandlerService
{
    public const ACTION = 'handler_auth_socialite';

    public function __construct(
        private UserSocialiteRepository $rUserSocialite,
        private PrepareSocialiteProviderService $providerService,
        private CoreAuthenticatableProvider $authenticatableProvider,
        private AuthenticatableRepository $rUser,
        private EventDispatcherInterface $eventDispatcher,
        private ConfigInterface $config,
        private ContainerInterface $container,
        private ?CorePolicyGuard $policyGuard,
    ) {
    }

    #[Transactional(attempts: 1)]
    public function run(
        SocialiteHandlerDTO $socialiteHandlerDTO,
        JwtGuard $jwtGuard,
        ?RequestInterface $request = null,
    ): AuthTokensDTO {
        $socialiteProvider = $this->providerService->run($socialiteHandlerDTO->provider, $socialiteHandlerDTO->app);
        $defaultRole = $this->config->get('socialite.apps.' . $socialiteHandlerDTO->app);
        $rolesToLogin = $this->config->get('auth.apps.' . $socialiteHandlerDTO->app, []);

        if (! empty($socialiteHandlerDTO->token)) {
            $socialiteUser = $socialiteProvider->userFromToken($socialiteHandlerDTO->token);
        } else {
            if (empty($request)) {
                throw new BusinessException(ErrorCode::VALIDATION_ERROR, __('exceptions.oauth.no_request'));
            }
            $socialiteProvider->setRequest($request);
            $socialiteUser = $socialiteProvider->user();
        }
        $providerUser = $this->makeUserSocialiteDto($socialiteUser, $socialiteHandlerDTO->provider);

        $userSocialite = $this->rUserSocialite->getByProviderData(
            $socialiteHandlerDTO->provider,
            $providerUser->provider_id
        );
        $isJustAssigned = false;
        $sessionUser = $this->authenticatableProvider->user();

        if (! empty($userSocialite)) {
            $user = $userSocialite->user;
            if (! empty($sessionUser) && $user->id != $sessionUser->getId()) {
                throw new BusinessException(ErrorCode::VALIDATION_ERROR, __('exceptions.oauth.account_already_assigned'));
            }
        } else {
            $isJustAssigned = true;
            $user = ! empty($sessionUser) ? $sessionUser : $this->rUser->getByEmail($providerUser->email);
            $user = empty($user)
                ? $this->createUserFromSocialite($providerUser, $defaultRole)
                : $this->assignUserToSocialite($user, $providerUser);
        }

        if (! empty($socialiteHandlerDTO->app) && ! in_array($user->getRole(), $rolesToLogin)) {
            throw new BusinessException(ErrorCode::VALIDATION_ERROR, __('exceptions.login.wrong_app'));
        }
        $this->policyGuard?->check('login', $user);

        $this->eventDispatcher->dispatch(new Action(self::ACTION, $userSocialite, [], $user));

        $tokens = $jwtGuard->login($user);
        if ($isJustAssigned && ! empty($providerUser->avatar_url)) {
            $this->assignSocialAvatar($user, $providerUser);
        }
        return $tokens;
    }

    private function createUserFromSocialite(UserSocialiteDTO $userSocialiteDTO, ?string $role): Authenticatable
    {
        if (empty($role)) {
            throw new BusinessException(ErrorCode::VALIDATION_ERROR, __('exceptions.oauth.sign_up_disabled'));
        }

        $createUserClassName = $this->config->get('socialite.services.create_user');
        if (empty($createUserClassName)) {
            throw new BusinessException(ErrorCode::VALIDATION_ERROR, __('exceptions.oauth.sign_up_disabled'));
        }
        $createUserService = $this->container->get($createUserClassName);
        if (! $createUserService instanceof CreateSocialiteUserService) {
            throw new BusinessException(ErrorCode::VALIDATION_ERROR, __('exceptions.oauth.sign_up_disabled'));
        }

        $user = $createUserService->run($userSocialiteDTO, $role);

        return $this->assignUserToSocialite($user, $userSocialiteDTO);
    }

    private function assignUserToSocialite(Authenticatable $user, UserSocialiteDTO $userSocialiteDTO): Authenticatable
    {
        $userSocialite = $this->rUserSocialite->create($userSocialiteDTO->toArray());
        $this->rUserSocialite->associate($userSocialite, 'user', $user);
        $this->rUserSocialite->save($userSocialite);

        return $user;
    }

    private function makeUserSocialiteDto(AbstractUser $user, string $provider): UserSocialiteDTO
    {
        $name = $user->getName();
        $nameParts = empty($name) ? ['', ''] : explode(' ', $name, 2);
        return UserSocialiteDTO::make([
            'email' => $user->getEmail(),
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? '',
            'avatar_url' => $user->getAvatar(),
            'provider_id' => $user->getId(),
            'provider_name' => $provider,
        ]);
    }

    private function assignSocialAvatar(Authenticatable $user, UserSocialiteDTO $userSocialiteDTO): void
    {
        $assignAvatarClassName = $this->config->get('socialite.services.assign_avatar');
        if (empty($assignAvatarClassName)) {
            return;
        }
        $assignAvatarService = $this->container->get($assignAvatarClassName);
        if (! $assignAvatarService instanceof AssignSocialiteAvatarService) {
            return;
        }
        try {
            $assignAvatarService->run($user, $userSocialiteDTO->avatar_url);
        } catch (\Throwable) {
            // ignore exceptions
        }
    }
}
