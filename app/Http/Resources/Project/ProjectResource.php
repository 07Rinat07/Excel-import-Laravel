<?php

namespace App\Http\Resources\Project;

use App\Http\Resources\Type\TypeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $values = $this->values->mapWithKeys(function ($value) {
            $key = $value->column?->key ?? $value->template_column_id;

            return [$key => $value->value];
        });

        return [
            'id' => $this->id,
            'type' => new TypeResource($this->type),
            'title' => $this->title,
            'row_index' => $this->row_index,
            'task_id' => $this->task_id,
            'template_id' => $this->template_id,
            'values' => $values,
        ];
    }
}
