<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Resource;

use OnixSystemsPHP\HyperfAuth\DTO\AuthTokensDTO;
use OnixSystemsPHP\HyperfCore\Resource\AbstractResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ResourceAuthToken",
 *     type="object",
 *     @OA\Property(property="access_token", type="string"),
 *     @OA\Property(property="refresh_token", type="string"),
 * )
 * @method __construct(AuthTokensDTO $resource)
 * @property AuthTokensDTO $resource
 */
class ResourceAuthToken extends AbstractResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
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
