<?php

namespace App\Modules\Autenticacao\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntrarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'ra' => ['required', 'string', 'max:40'],
            'password' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('ra')) {
            $this->merge([
                'ra' => mb_strtoupper((string) $this->input('ra')),
            ]);
        }
    }
}
