<?php

namespace App\Http\Resources\Task;

use App\Http\Resources\File\FileResource;
use App\Http\Resources\User\UserResource;
use App\Models\Task;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'file' => new FileResource($this->file),
            'status' => Task::getStatuses()[$this->status] ?? 'Unknown status',
            'status_code' => $this->status,
            'failed_rows_count' => $this->failed_rows_count,
            'total_rows' => $this->total_rows,
            'imported_rows' => $this->imported_rows,
            'type_id' => $this->type_id,
            'template_id' => $this->template_id,
            'type' => $this->typeModel ? [
                'id' => $this->typeModel->id,
                'title' => $this->typeModel->title,
            ] : null,
            'template' => $this->template ? [
                'id' => $this->template->id,
                'name' => $this->template->name,
            ] : null,

        ];
    }
}
