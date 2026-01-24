<?php

namespace App\Http\Resources\Template;

use Illuminate\Http\Resources\Json\JsonResource;

class TemplateColumnResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'label' => $this->label,
            'data_type' => $this->data_type,
            'is_required' => (bool) $this->is_required,
            'position' => $this->position,
        ];
    }
}
