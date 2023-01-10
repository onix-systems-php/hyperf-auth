<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Request;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RequestAppHandler',
    properties: [
        new OA\Property(property: 'provider', type: 'string', example: 'google'),
        new OA\Property(property: 'app', type: 'string', example: 'admin', description: 'Requesting app keyname'),
        new OA\Property(property: 'token', type: 'string'),
    ],
    type: 'object',
)]
class RequestAppHandler extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $config = $this->container->get(ConfigInterface::class);
        $providers = $config->get('socialite.active_providers');
        $apps = array_keys($config->get('socialite.apps', []));

        $rule = 'required|string';
        return [
            'provider' => [
                'required',
                'string',
                'in:' . $providers,
            ],
            'app' => [
                'required',
                'string',
                Rule::in($apps),
            ],
            'token' => $rule,
        ];
    }
}
