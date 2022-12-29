<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Contract;

use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatable;

interface Authenticatable extends CoreAuthenticatable, \Qbhy\HyperfAuth\Authenticatable
{
    public function getPassword(): string;
}
