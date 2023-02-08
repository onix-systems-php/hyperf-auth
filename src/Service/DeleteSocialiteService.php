<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Service;

use Hyperf\DbConnection\Annotation\Transactional;
use OnixSystemsPHP\HyperfActionsLog\Event\Action;
use OnixSystemsPHP\HyperfAuth\Repository\UserSocialiteRepository;
use OnixSystemsPHP\HyperfCore\Contract\CorePolicyGuard;
use OnixSystemsPHP\HyperfCore\Service\Service;
use Psr\EventDispatcher\EventDispatcherInterface;

#[Service]
class DeleteSocialiteService
{
    public const ACTION = 'disconnect_social';

    public function __construct(
        private UserSocialiteRepository $rUserSocial,
        private ?CorePolicyGuard $policyGuard,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[Transactional(attempts: 1)]
    public function run(string $providerName, int $userId): void
    {
        $social = $this->rUserSocial->getByUserProvider($providerName, $userId, true, true);
        $providerName = $social?->provider_name;
        $this->policyGuard?->check('delete', $social);
        $this->rUserSocial->delete($social);
        $this->eventDispatcher->dispatch(new Action(self::ACTION, $social, ['provider' => $providerName]));
    }
}
