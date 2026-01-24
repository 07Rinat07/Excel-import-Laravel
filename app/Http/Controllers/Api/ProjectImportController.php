<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\ImportStoreRequest;
use App\Jobs\ImportProjectExcelFileJob;
use App\Models\File;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class ProjectImportController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/projects/import",
     *     tags={"Projects"},
     *     summary="Import projects from an Excel file",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 required={"file","type_id"},
     *
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="XLSX, CSV or TSV file"
     *                 ),
     *                 @OA\Property(
     *                     property="type_id",
     *                     type="integer",
     *                     example=1
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=202,
     *         description="Import accepted"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(ImportStoreRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Project::class);

        $data = $request->validated();

        $file = File::putAndCreate($data['file']);
        $task = Task::create([
            'file_id' => $file->id,
            'user_id' => $request->user()->id,
            'type' => 1,
            'type_id' => $data['type_id'],
            'status' => Task::STATUS_PROCESS,
        ]);

        ImportProjectExcelFileJob::dispatch($file->path, $task)->onQueue('imports');

        return response()->json([
            'message' => 'Excel import in process',
            'task_id' => $task->id,
        ], 202);
    }
}
