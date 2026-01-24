<?php

namespace App\Http\Resources\Template;

use Illuminate\Http\Resources\Json\JsonResource;

class ExcelTemplateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type_id' => $this->type_id,
            'is_active' => (bool) $this->is_active,
            'columns' => TemplateColumnResource::collection($this->whenLoaded('columns')),
        ];
    }
}
