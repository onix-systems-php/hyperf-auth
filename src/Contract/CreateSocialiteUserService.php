<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Contract;

use OnixSystemsPHP\HyperfAuth\DTO\UserSocialiteDTO;

interface CreateSocialiteUserService
{
    public function run(UserSocialiteDTO $user, string $role): Authenticatable;
}
