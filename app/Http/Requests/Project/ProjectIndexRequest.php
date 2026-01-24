<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class ProjectIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type_id' => 'nullable|integer|exists:types,id',
            'task_id' => 'nullable|integer|exists:tasks,id',
            'template_id' => 'nullable|integer|exists:excel_templates,id',
            'q' => 'nullable|string|max:255',
            'filters' => 'array',
            'filters.*' => 'nullable|string|max:255',
        ];
    }
}
