<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\DTO;

use OnixSystemsPHP\HyperfCore\DTO\AbstractDTO;

class SocialiteHandlerDTO extends AbstractDTO
{
    public string $provider;

    public string $app;

    public string $token;
}
