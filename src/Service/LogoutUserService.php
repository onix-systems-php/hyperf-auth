<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Service;

use Hyperf\DbConnection\Annotation\Transactional;
use OnixSystemsPHP\HyperfActionsLog\Event\Action;
use OnixSystemsPHP\HyperfAuth\AuthManager;
use OnixSystemsPHP\HyperfCore\Service\Service;
use Psr\EventDispatcher\EventDispatcherInterface;

#[Service]
class LogoutUserService
{
    public const ACTION = 'logout';

    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    #[Transactional(attempts: 1)]
    public function run(AuthManager $authManager): void
    {
        $user = $authManager->user();
        if (! empty($user)) {
            $authManager->logout();
            $this->eventDispatcher->dispatch(new Action(self::ACTION, $user, [], $user));
        }
    }
}
