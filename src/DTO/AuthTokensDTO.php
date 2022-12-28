<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\DTO;

use OnixSystemsPHP\HyperfCore\DTO\AbstractDTO;

class AuthTokensDTO extends AbstractDTO
{
    public string $access_token;

    public string $refresh_token;
}
