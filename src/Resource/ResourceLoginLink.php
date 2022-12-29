<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Resource;

use OnixSystemsPHP\HyperfCore\Resource\AbstractResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ResourceLoginLink",
 *     type="object",
 *     @OA\Property(property="link", type="string"),
 * )
 * @method __construct(string $resource)
 * @property string $resource
 */
class ResourceLoginLink extends AbstractResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'link' => $this->resource,
        ];
    }
}
