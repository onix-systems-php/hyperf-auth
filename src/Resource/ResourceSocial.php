<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Resource;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\ApplicationContext;
use OnixSystemsPHP\HyperfAuth\Model\UserSocialite;
use OnixSystemsPHP\HyperfCore\Resource\AbstractResource;
use OpenApi\Attributes as OA;

/**
 * @method __construct(UserSocialite $resource)
 * @property UserSocialite $resource
 */
#[OA\Schema(
    schema: 'ResourceSocial',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'email', type: 'string'),
        new OA\Property(property: 'provider_id', type: 'string'),
        new OA\Property(property: 'provider_name', type: 'string'),
        new OA\Property(property: 'user', ref: '#/components/schemas/ResourceUser'),
        new OA\Property(property: 'created_at', type: 'string'),
        new OA\Property(property: 'updated_at', type: 'string'),
    ],
    type: 'object',
)]
class ResourceSocial extends AbstractResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(): array
    {
        /** @var AbstractResource $class */
        $class = ApplicationContext::getContainer()->get(ConfigInterface::class)->get('socialite.user_resource');

        $result = [
            'id' => $this->resource->id,
            'email' => $this->resource->email,
            'provider_id' => $this->resource->provider_id,
            'provider_name' => $this->resource->provider_name,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
        if (! empty($class)) {
            $result['user'] = $class::make($this->resource->user);
        }
        return $result;
    }
}
