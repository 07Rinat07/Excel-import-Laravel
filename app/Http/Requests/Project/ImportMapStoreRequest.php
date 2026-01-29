<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ImportMapStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'columns' => 'required|array',
            'columns.*.index' => 'required|integer|min:0',
            'columns.*.include' => 'required|boolean',
            'columns.*.name' => 'nullable|string|max:120',
            'columns.*.data_type' => 'required|string|in:string,number,integer,date,boolean',
            'columns.*.required' => 'required|boolean',
            'columns.*.validation_rules' => 'nullable|string|max:500',
            'sheet_index' => 'nullable|integer|min:0',
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
