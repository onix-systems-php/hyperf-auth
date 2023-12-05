<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Contract;

use OnixSystemsPHP\HyperfCore\Contract\CoreAuthenticatable;

interface Authenticatable extends CoreAuthenticatable, \Qbhy\HyperfAuth\Authenticatable
{
    public function getPassword(): ?string;

    public function toArray(): array;
}
