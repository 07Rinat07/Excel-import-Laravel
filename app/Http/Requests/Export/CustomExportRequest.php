<?php

namespace App\Http\Requests\Export;

use Illuminate\Foundation\Http\FormRequest;

class CustomExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'source_type' => 'required|string|in:type,task',
            'source_id' => 'required|integer',
            'format' => 'required|string|in:xlsx,csv,tsv',
            'user_id' => 'nullable|integer|exists:users,id',
            'sheet_name' => 'nullable|string|max:120',
            'sheet_index' => 'nullable|integer|min:0',
            'columns' => 'required|array|min:1',
            'columns.*' => 'integer',
            'labels' => 'nullable|array',
            'labels.*' => 'nullable|string|max:120',
        ];
    }
}
