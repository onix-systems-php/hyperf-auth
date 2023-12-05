<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\DTO;

use OnixSystemsPHP\HyperfCore\DTO\AbstractDTO;

class LoginDTO extends AbstractDTO
{
    public string $login;

    public string $password;

    public ?string $app;
}
