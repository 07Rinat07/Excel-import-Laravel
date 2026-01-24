<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TemplateUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $template = $this->route('template');
        $templateId = $template?->id;

        return [
            'name' => 'required|string|max:255',
            'type_id' => 'nullable|integer|exists:types,id',
            'is_active' => 'boolean',
            'columns' => 'array',
            'columns.*.id' => [
                'nullable',
                'integer',
                Rule::exists('excel_template_columns', 'id')->where('template_id', $templateId),
            ],
            'columns.*.label' => 'required|string|max:255',
            'columns.*.key' => 'nullable|string|max:255',
            'columns.*.data_type' => 'required|string|in:string,number,integer,date,boolean',
            'columns.*.is_required' => 'boolean',
            'columns.*.position' => 'integer|min:0',
            'deleted_ids' => 'array',
            'deleted_ids.*' => [
                'integer',
                Rule::exists('excel_template_columns', 'id')->where('template_id', $templateId),
            ],
        ];
    }
}
