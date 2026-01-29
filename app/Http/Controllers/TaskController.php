<?php

namespace App\Http\Controllers;

use App\Http\Resources\FailedRow\FailedRowResource;
use App\Http\Resources\Task\TaskResource;
use App\Models\FailedRow;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $tasks = Task::query()
            ->visibleTo($request->user())
            ->with(['user', 'file', 'typeModel', 'template'])
            ->withCount('failedRows')
            ->paginate(5);

        $tasks = TaskResource::collection($tasks);

        return inertia('Task/Index', compact('tasks'));
    }

    public function failedList(Request $request, Task $task)
    {
        $this->authorize('view', $task);

        $failedRows = FailedRow::where('task_id', $task->id)->paginate(10);

        $failedList = FailedRowResource::collection($failedRows);

        $templateColumns = $task->template
            ? $task->template->columns()->orderBy('position')->get()->map(function ($column) {
                return [
                    'key' => $column->key,
                    'label' => $column->label,
                    'data_type' => $column->data_type,
                    'is_required' => (bool) $column->is_required,
                    'validation_rules' => $column->validation_rules ?? [],
                ];
            })
            : collect();

        return inertia('Task/FailedList', [
            'failedList' => $failedList,
            'taskId' => $task->id,
            'templateColumns' => $templateColumns,
        ]);
    }
}
