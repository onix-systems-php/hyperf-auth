<?php

declare(strict_types=1);
namespace OnixSystemsPHP\HyperfAuth\Request;

use Hyperf\Validation\Request\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RequestRefresh',
    properties: [
        new OA\Property(property: 'access_token', type: 'string', example: '<token>'),
        new OA\Property(property: 'refresh_token', type: 'string', example: '<token>'),
    ],
    type: 'object',
)]
class RequestRefresh extends FormRequest
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
        $rule = 'required|string';
        return [
            'access_token' => $rule,
            'refresh_token' => $rule,
        ];
    }
}
