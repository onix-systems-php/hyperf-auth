<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Request;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="RequestAppHandler",
 *     type="object",
 *     @OA\Property(property="provider", type="string", example="google"),
 *     @OA\Property(property="app", type="string", example="admin"),
 *     @OA\Property(property="token", type="string")
 * )
 */
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
