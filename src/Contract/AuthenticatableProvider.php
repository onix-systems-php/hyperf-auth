<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Contract;

use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatableProvider;

interface AuthenticatableProvider extends CoreAuthenticatableProvider
{
    public function user(): Authenticatable|null;
}
