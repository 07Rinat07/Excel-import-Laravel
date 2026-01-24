<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Type;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Общая статистика
        $stats = [
            'total_projects' => Project::visibleTo($user)->count(),
            'total_tasks' => Task::visibleTo($user)->count(),
            'success_tasks' => Task::visibleTo($user)->where('status', Task::STATUS_SUCCESS)->count(),
            'error_tasks' => Task::visibleTo($user)->where('status', Task::STATUS_ERROR)->count(),
        ];

        // Распределение проектов по типам
        $projectsByType = Type::withCount(['projects' => function ($query) use ($user) {
            $query->visibleTo($user);
        }])->get()
            ->map(fn($type) => [
                'label' => $type->title,
                'count' => $type->projects_count
            ]);

        // Динамика импортов за последние 7 дней
        $importDynamics = Task::visibleTo($user)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Последние задачи
        $recentTasks = Task::visibleTo($user)
            ->with(['typeModel', 'user'])
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'projectsByType' => $projectsByType,
            'importDynamics' => $importDynamics,
            'recentTasks' => $recentTasks,
        ]);
    }
}
