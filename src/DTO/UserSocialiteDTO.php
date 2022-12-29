<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\DTO;

use OnixSystemsPHP\HyperfCore\DTO\AbstractDTO;

class UserSocialiteDTO extends AbstractDTO
{
    public string $email;

    public ?string $first_name;

    public ?string $last_name;

    public ?string $avatar_url;

    public string $provider_id;

    public string $provider_name;
}
