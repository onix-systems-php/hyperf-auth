<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfAuth\Resource;

use OnixSystemsPHP\HyperfCore\Resource\AbstractResource;
use OpenApi\Attributes as OA;

/**
 * @method __construct(string $resource)
 * @property string $resource
 */
#[OA\Schema(
    schema: 'ResourceLoginLink',
    properties: [
        new OA\Property(property: 'link', type: 'string'),
    ],
    type: 'object',
)]
class ResourceLoginLink extends AbstractResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(): array
    {
        return [
            'link' => $this->resource,
        ];
    }
}
