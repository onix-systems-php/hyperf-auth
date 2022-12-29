<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Contract;

use OnixSystemsPHP\HyperfAuth\DTO\UserSocialiteDTO;

interface CreateSocialiteUserService
{
    public function run(UserSocialiteDTO $user, string $role): Authenticatable;
}
