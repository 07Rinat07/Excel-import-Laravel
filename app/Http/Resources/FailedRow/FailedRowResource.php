<?php

namespace App\Http\Resources\FailedRow;

use Illuminate\Http\Resources\Json\JsonResource;

class FailedRowResource extends JsonResource
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
            'key' => $this->key,
            'row' => $this->row ?? $this->row_number,
            'row_number' => $this->row_number ?? $this->row,
            'message' => $this->message,
            'data' => $this->data ?? $this->row ?? [],
            'errors' => $this->errors ?? $this->error_messages ?? [],
            'corrected_data' => $this->corrected_data,
            'is_corrected' => (bool) $this->is_corrected,
            'task_id' => $this->task_id,
            'created_at' => $this->created_at->format('Y-m-d'),

        ];
    }
}
