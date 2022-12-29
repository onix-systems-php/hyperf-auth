<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Service;

use Hyperf\DbConnection\Annotation\Transactional;
use OnixSystemsPHP\HyperfAuth\Repository\UserSocialiteRepository;
use OnixSystemsPHP\HyperfCore\Service\Service;

#[Service]
class DeleteSocialiteService
{
    public function __construct(private UserSocialiteRepository $rUserSocial)
    {
    }

    #[Transactional(attempts: 1)]
    public function run(string $providerName, int $userId): void
    {
        $social = $this->rUserSocial->getByUserProvider($providerName, $userId, true, true);
        $this->rUserSocial->delete($social);
    }
}
