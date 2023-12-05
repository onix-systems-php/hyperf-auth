<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Model;

use Carbon\Carbon;
use OnixSystemsPHP\HyperfCore\Model\AbstractOwnedModel;

/**
 * @property int $id
 * @property string $provider_name
 * @property string $provider_id
 * @property string $email
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class UserSocialite extends AbstractOwnedModel
{
    protected ?string $table = 'user_socialites';

    protected array $fillable = [
        'user_id', 'provider_name', 'provider_id', 'email',
    ];

    protected array $casts = [
        'id' => 'integer',
        'email' => 'string',
        'provider_id' => 'string',
        'provider_name' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
