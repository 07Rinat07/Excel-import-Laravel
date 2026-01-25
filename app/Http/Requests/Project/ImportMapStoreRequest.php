<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

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
        ];
    }
}
