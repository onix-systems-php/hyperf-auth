<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\DTO;

use OnixSystemsPHP\HyperfCore\DTO\AbstractDTO;

class LoginDTO extends AbstractDTO
{
    public string $login;
    public string $password;
    public ?string $app;
}
