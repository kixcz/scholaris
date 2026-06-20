<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Notebook;
use App\Models\Pillar;
use App\Models\Reference;
use App\Models\Roadmap;
use App\Models\Vocabulary;
use App\Models\WeeklyGoal;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $userId = auth()->id();

        // Get stats
        $stats = [
            'total_papers' => Reference::where('user_id', $userId)->count(),
            'total_notebooks' => Notebook::where('user_id', $userId)->count(),
            'total_vocabulary' => Vocabulary::where('user_id', $userId)->count(),
            'total_roadmaps' => Roadmap::where('user_id', $userId)->count(),
            'total_links' => Link::where('user_id', $userId)->count(),
            'active_pillars' => Pillar::where('user_id', $userId)->where('is_active', true)->count(),
            'weekly_goals_completed' => WeeklyGoal::where('user_id', $userId)
                ->where('status', 'completed')
                ->count(),
            'reading_progress' => 65, // Placeholder - calculate from references
        ];

        // Get pillars with counts
        $pillars = Pillar::where('user_id', $userId)
            ->where('is_active', true)
            ->with(['domains'])
            ->withCount(['references', 'vocabulary', 'links'])
            ->ordered()
            ->get();

        // Get recent papers
        $recentPapers = Reference::where('user_id', $userId)
            ->with('pillar')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($ref) {
                return [
                    'title' => $ref->title,
                    'status' => $ref->status,
                    'pillar' => $ref->pillar?->name ?? 'Uncategorized',
                ];
            });

        // Calculate weekly goal progress
        $allWeekGoals = WeeklyGoal::where('user_id', $userId)->get();

        $weeklyGoalProgress = $allWeekGoals->count() > 0
            ? round(($allWeekGoals->where('status', 'completed')->count() / $allWeekGoals->count()) * 100)
            : 0;

        return Inertia::render('dashboard', [
            'pillars' => $pillars,
            'stats' => $stats,
            'recentPapers' => $recentPapers,
            'weeklyGoalProgress' => $weeklyGoalProgress,
        ]);
    }
}
