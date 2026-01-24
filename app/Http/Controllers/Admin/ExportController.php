<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExportLog;
use Inertia\Inertia;
use Inertia\Response;

class ExportController extends Controller
{
    public function index(): Response
    {
        $exports = ExportLog::query()
            ->with('user')
            ->latest('id')
            ->paginate(15)
            ->through(function (ExportLog $log) {
                return [
                    'id' => $log->id,
                    'source_type' => $log->source_type,
                    'source_id' => $log->source_id,
                    'format' => $log->format,
                    'status' => $log->status,
                    'file_name' => $log->file_name,
                    'user' => $log->user ? [
                        'id' => $log->user->id,
                        'name' => $log->user->name,
                        'email' => $log->user->email,
                    ] : null,
                    'created_at' => $log->created_at?->format('Y-m-d H:i'),
                ];
            });

        return Inertia::render('Admin/Exports/Index', [
            'exports' => $exports,
        ]);
    }
}
