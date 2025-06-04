<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePerfumeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // For now, allow all requests.
        // Later, this should check if the authenticated user is an admin.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'brand' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'json'],
            'image_url' => ['nullable', 'string', 'max:255'], // Consider 'url' validation
            'concentration' => ['nullable', 'string', 'max:255'],
            'gender_affinity' => ['nullable', 'string', 'max:255'],
            'launch_year' => ['nullable', 'integer', 'digits:4', 'min:1000', 'max:' . (date('Y') + 1)],
        ];
    }
}
