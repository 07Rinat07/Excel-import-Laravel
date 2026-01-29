<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ImportStoreRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('type_id') && $this->input('type_id') === '') {
            $this->merge(['type_id' => null]);
        }
    }

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
            'file' => 'required|file|mimes:xlsx,xls,xlsm,csv,tsv,txt|max:51200',
            'type_id' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'The file must be an .xlsx, .xls, .xlsm, .csv or .tsv document.',
            'file.max' => 'The file size must not exceed 50MB.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->ajax()) {
            throw new HttpResponseException(response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422));
        }

        parent::failedValidation($validator);
    }
}
