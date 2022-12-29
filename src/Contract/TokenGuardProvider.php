<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Contract;

interface TokenGuardProvider
{
    public function tokenGuard(): TokenGuard;
}
