<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Test\Mocks;

use OnixSystemsPHP\HyperfAuth\Contract\Authenticatable;
use OnixSystemsPHP\HyperfCore\Model\AbstractModel;

abstract class AuthenticatableModel extends AbstractModel implements Authenticatable
{
    abstract public function getPassword(): ?string;

    abstract public function getId();

    abstract public function getRole(): string;
}
