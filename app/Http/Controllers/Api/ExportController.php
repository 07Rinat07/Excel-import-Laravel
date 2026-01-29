<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExportLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\Type;
use App\Services\Export\ProjectExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'source_type' => 'required|in:project,task,type',
            'source_id' => 'required|integer',
            'format' => 'required|in:xlsx,csv,tsv',
        ]);

        match ($data['source_type']) {
            'project' => $this->resolveProject($request, (int) $data['source_id']),
            'task' => $this->resolveTask($request, (int) $data['source_id']),
            'type' => $this->resolveType($request, (int) $data['source_id']),
            default => abort(404),
        };

        $filename = "{$data['source_type']}-{$data['source_id']}.{$data['format']}";

        $log = ExportLog::create([
            'user_id' => $request->user()?->id,
            'source_type' => $data['source_type'],
            'source_id' => $data['source_id'],
            'format' => $data['format'],
            'status' => 'pending',
            'file_name' => $filename,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $log->id,
                'status' => $log->status,
            ],
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $log = ExportLog::findOrFail($id);
        $this->authorizeLog($request, $log);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $log->id,
                'status' => $log->status,
                'source_type' => $log->source_type,
                'source_id' => $log->source_id,
                'format' => $log->format,
                'file_name' => $log->file_name,
                'failure_reason' => $log->failure_reason,
                'created_at' => $log->created_at?->toISOString(),
            ],
        ]);
    }

    public function download(Request $request, int $id, ProjectExportService $service): BinaryFileResponse
    {
        $log = ExportLog::findOrFail($id);
        $this->authorizeLog($request, $log);

        $filename = $log->file_name ?: "{$log->source_type}-{$log->source_id}.{$log->format}";

        try {
            $response = match ($log->source_type) {
                'project' => $service->exportByProject(
                    $this->resolveProject($request, (int) $log->source_id),
                    $log->format
                ),
                'task' => $service->exportByTask(
                    $this->resolveTask($request, (int) $log->source_id),
                    $log->format
                ),
                'type' => $service->exportByType(
                    $this->resolveType($request, (int) $log->source_id),
                    $log->format,
                    $request->user()
                ),
                default => abort(404),
            };

            $log->update([
                'status' => 'success',
                'file_name' => $filename,
                'failure_reason' => null,
            ]);

            return $response;
        } catch (\Throwable $e) {
            $log->update([
                'status' => 'failed',
                'file_name' => $filename,
                'failure_reason' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function authorizeLog(Request $request, ExportLog $log): void
    {
        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        if (! $user->isAdmin() && $log->user_id !== $user->id) {
            abort(403);
        }
    }

    private function resolveProject(Request $request, int $id): Project
    {
        $project = Project::findOrFail($id);
        $this->authorize('view', $project);

        return $project;
    }

    private function resolveTask(Request $request, int $id): Task
    {
        $task = Task::findOrFail($id);
        $this->authorize('view', $task);

        return $task;
    }

    private function resolveType(Request $request, int $id): Type
    {
        $type = Type::findOrFail($id);
        $this->authorize('export', $type);

        return $type;
    }
}
