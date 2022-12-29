<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Contract;

interface AuthenticatableRepository
{
    public function getByLogin(string $login): Authenticatable|null;

    public function getByEmail(string $email): Authenticatable|null;
}
