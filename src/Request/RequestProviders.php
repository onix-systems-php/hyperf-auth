<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Request;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="RequestProviders",
 *     type="object",
 *     @OA\Property(property="provider", type="string", example="google"),
 *     @OA\Property(property="app", type="string", example="admin", description="Requesting app keyname"),
 * )
 */
class RequestProviders extends FormRequest
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
        ];
    }

    /**
     * Adding parameter from route.
     */
    public function all(): array
    {
        $request = parent::all();
        $request['provider'] = $this->route('provider');
        $request['app'] = $this->route('app');

        return $request;
    }
}
