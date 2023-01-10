<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Request;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RequestLogin',
    properties: [
        new OA\Property(property: 'login', type: 'string', example: 'example@gmail.com'),
        new OA\Property(property: 'password', type: 'string', example: 'password123'),
        new OA\Property(property: 'app', type: 'string', example: 'admin', description: 'Requesting app keyname'),
    ],
    type: 'object',
)]
class RequestLogin extends FormRequest
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
        $apps = array_keys($config->get('auth.apps', []));

        $rule = 'required|string';
        return [
            'login' => $rule,
            'password' => $rule,
            'app' => [
                'string',
                'required',
                Rule::in($apps),
            ],
        ];
    }
}
