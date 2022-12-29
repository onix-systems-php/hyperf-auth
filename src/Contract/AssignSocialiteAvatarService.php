<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Contract;

interface AssignSocialiteAvatarService
{
    public function run(Authenticatable $user, string $avatarUrl);
}
