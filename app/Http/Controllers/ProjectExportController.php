<?php

namespace App\Http\Controllers;

use App\Models\ExportLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\Type;
use App\Services\Export\ProjectExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProjectExportController extends Controller
{
    private ProjectExportService $service;

    public function __construct(ProjectExportService $service)
    {
        $this->service = $service;
    }

    public function project(Request $request, Project $project, string $format): BinaryFileResponse
    {
        $this->authorize('view', $project);
        $format = $this->normalizeFormat($format);
        $filename = "project-{$project->id}.{$format}";

        try {
            $response = $this->service->exportByProject($project, $format);
            $this->logExport($request->user(), 'project', $project->id, $format, 'success', $filename);

            return $response;
        } catch (\Throwable $e) {
            $this->logExport($request->user(), 'project', $project->id, $format, 'failed', $filename);
            throw $e;
        }
    }

    public function task(Request $request, Task $task, string $format): BinaryFileResponse
    {
        $this->authorize('view', $task);
        $format = $this->normalizeFormat($format);
        $filename = "task-{$task->id}.{$format}";

        try {
            $response = $this->service->exportByTask($task, $format);
            $this->logExport($request->user(), 'task', $task->id, $format, 'success', $filename);

            return $response;
        } catch (\Throwable $e) {
            $this->logExport($request->user(), 'task', $task->id, $format, 'failed', $filename);
            throw $e;
        }
    }

    public function type(Request $request, Type $type, string $format): BinaryFileResponse
    {
        $this->authorize('export', $type);
        $format = $this->normalizeFormat($format);
        $filename = "type-{$type->id}.{$format}";

        try {
            $response = $this->service->exportByType($type, $format, $request->user());
            $this->logExport($request->user(), 'type', $type->id, $format, 'success', $filename);

            return $response;
        } catch (\Throwable $e) {
            $this->logExport($request->user(), 'type', $type->id, $format, 'failed', $filename);
            throw $e;
        }
    }

    private function normalizeFormat(string $format): string
    {
        $format = strtolower($format);
        if (! in_array($format, ['xlsx', 'csv', 'tsv'], true)) {
            abort(404);
        }

        return $format;
    }

    private function logExport(?\App\Models\User $user, string $sourceType, int $sourceId, string $format, string $status, string $fileName): void
    {
        ExportLog::create([
            'user_id' => $user?->id,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'format' => $format,
            'status' => $status,
            'file_name' => $fileName,
        ]);
    }
}
