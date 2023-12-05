<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Resource;

use OnixSystemsPHP\HyperfAuth\DTO\AuthTokensDTO;
use OnixSystemsPHP\HyperfCore\Resource\AbstractResource;
use OpenApi\Attributes as OA;

/**
 * @method __construct(AuthTokensDTO $resource)
 * @property AuthTokensDTO $resource
 */
#[OA\Schema(
    schema: 'ResourceAuthToken',
    properties: [
        new OA\Property(property: 'access_token', type: 'string'),
        new OA\Property(property: 'refresh_token', type: 'string'),
    ],
    type: 'object',
)]
class ResourceAuthToken extends AbstractResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(): array
    {
        $r = $this->resource;
        return [
            'access_token' => $r->access_token,
            'refresh_token' => $r->refresh_token,
        ];
    }
}
