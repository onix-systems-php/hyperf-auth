<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Contract;

interface AuthenticatableRepository
{
    public function getByLogin(string $login): null|Authenticatable;

    public function getByEmail(string $email): null|Authenticatable;
}
