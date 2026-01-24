<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class ImportStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,csv,tsv,txt|max:10240',
            'type_id' => 'required|integer|exists:types,id',
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'The file must be an .xlsx, .csv or .tsv document.',
            'file.max' => 'The file size must not exceed 10MB.',
        ];
    }
}
