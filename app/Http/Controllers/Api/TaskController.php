<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FailedRow\FailedRowResource;
use App\Http\Resources\Task\TaskResource;
use App\Models\FailedRow;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

class TaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="List import tasks",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Task::class);

        $tasks = Task::query()
            ->visibleTo($request->user())
            ->with(['user', 'file', 'typeModel', 'template'])
            ->withCount('failedRows')
            ->paginate(10);

        return TaskResource::collection($tasks);
    }

    /**
     * @OA\Get(
     *     path="/api/tasks/{task}/failed-rows",
     *     tags={"Tasks"},
     *     summary="List failed rows for a task",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function failedRows(Request $request, Task $task): AnonymousResourceCollection
    {
        $this->authorize('view', $task);

        $failedRows = FailedRow::where('task_id', $task->id)->paginate(10);

        return FailedRowResource::collection($failedRows);
    }

    /**
     * @OA\Get(
     *     path="/api/tasks/{task}",
     *     tags={"Tasks"},
     *     summary="Get a task by ID",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function show(Task $task): TaskResource
    {
        $this->authorize('view', $task);

        $task->loadMissing(['user', 'file', 'typeModel', 'template'])->loadCount('failedRows');

        return new TaskResource($task);
    }
}
