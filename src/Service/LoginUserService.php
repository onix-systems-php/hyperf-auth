<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Service;

use Hyperf\Contract\ConfigInterface;
use Hyperf\DbConnection\Annotation\Transactional;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use OnixSystemsPHP\HyperfActionsLog\Event\Action;
use OnixSystemsPHP\HyperfAuth\Contract\Authenticatable;
use OnixSystemsPHP\HyperfAuth\Contract\AuthenticatableRepository;
use OnixSystemsPHP\HyperfAuth\Contract\TokenGuard;
use OnixSystemsPHP\HyperfAuth\DTO\AuthTokensDTO;
use OnixSystemsPHP\HyperfAuth\DTO\LoginDTO;
use OnixSystemsPHP\HyperfCore\Constants\ErrorCode;
use OnixSystemsPHP\HyperfCore\Contract\CorePolicyGuard;
use OnixSystemsPHP\HyperfCore\Exception\BusinessException;
use OnixSystemsPHP\HyperfCore\Service\Service;
use Psr\EventDispatcher\EventDispatcherInterface;
use Qbhy\SimpleJwt\Interfaces\Encrypter;

#[Service]
class LoginUserService
{
    public const ACTION = 'login';

    public function __construct(
        private ValidatorFactoryInterface $vf,
        private Encrypter $encrypter,
        private AuthenticatableRepository $rUser,
        private EventDispatcherInterface $eventDispatcher,
        private ConfigInterface $config,
        private ?CorePolicyGuard $policyGuard,
    ) {
    }

    #[Transactional(attempts: 1)]
    public function run(LoginDTO $params, TokenGuard $jwtGuard): AuthTokensDTO
    {
        $this->validate($params);
        $user = $this->rUser->getByLogin($params->login);
        $this->validateUser($user, $params);
        $authTokensDTO = $jwtGuard->login($user);
        $this->eventDispatcher->dispatch(new Action(self::ACTION, $user, [], $user));
        return $authTokensDTO;
    }

    private function validate(LoginDTO $params): void
    {
        $this->vf
            ->make($params->toArray(), [
                'login' => $this->config->get('auth.validators.login', 'required|email'),
                'password' => $this->config->get('auth.validators.password', 'required|min:6'),
            ])
            ->validate();
    }

    private function validateUser(?Authenticatable $user, LoginDTO $params): void
    {
        if (empty($user)) {
            throw new BusinessException(ErrorCode::VALIDATION_ERROR, __('exceptions.login.wrong_password'));
        }

        if (! in_array($user->getRole(), $this->config->get('auth.apps.' . $params->app, []))) {
            throw new BusinessException(ErrorCode::VALIDATION_ERROR, __('exceptions.login.wrong_app'));
        }
        if (! $this->encrypter->check($params->password, $user->getPassword())) {
            throw new BusinessException(ErrorCode::VALIDATION_ERROR, __('exceptions.login.wrong_password'));
        }
        $this->policyGuard?->check('login', $user);
    }
}
