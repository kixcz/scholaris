<?php

namespace App\Services;

use App\Models\ProductivityMetric;
use App\Models\ProductivitySession;
use App\Models\WeeklyGoal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProductivityTracker
{
    /**
     * Aggregate daily metrics from sessions for a specific date
     */
    public function aggregateDailyMetrics(Carbon $date = null): ProductivityMetric
    {
        $date = $date ?? Carbon::now();
        $userId = auth()->id();

        Log::info("Aggregating daily metrics for: {$date->format('Y-m-d')}");

        // Get or create metric record
        $metric = ProductivityMetric::firstOrCreate(
            [
                'user_id' => $userId,
                'metric_date' => $date->format('Y-m-d'),
            ],
            [
                'tasks_completed' => 0,
                'references_reviewed' => 0,
                'tutorials_completed' => 0,
                'vocabulary_learned' => 0,
                'notes_created' => 0,
                'deep_work_minutes' => 0,
                'total_study_minutes' => 0,
                'total_points' => 0,
            ]
        );

        // Recalculate from sessions
        $metric->recalculateFromSessions();

        Log::info("Daily metrics calculated. Score: {$metric->productivity_score}");

        return $metric;
    }

    /**
     * Get productivity trends for last N weeks
     */
    public function getWeeklyTrends(int $weeks = 4): array
    {
        $trends = ProductivityMetric::getWeeklyTrends($weeks);

        // Calculate week-over-week change
        for ($i = 1; $i < count($trends); $i++) {
            $current = $trends[$i]['total_points'];
            $previous = $trends[$i - 1]['total_points'];
            
            if ($previous > 0) {
                $trends[$i]['change_percentage'] = round((($current - $previous) / $previous) * 100, 2);
            } else {
                $trends[$i]['change_percentage'] = $current > 0 ? 100 : 0;
            }
        }

        return $trends;
    }

    /**
     * Get comprehensive dashboard data
     */
    public function getDashboardData(): array
    {
        $today = Carbon::now();
        $weekStart = $today->copy()->startOfWeek();
        $lastWeekStart = $weekStart->copy()->subWeek();

        // Today's metrics
        $todayMetrics = ProductivityMetric::getTodayMetrics();
        
        // This week's metrics
        $thisWeekMetrics = ProductivityMetric::getThisWeekMetrics();
        
        // Last week's metrics (for comparison)
        $lastWeekMetrics = ProductivityMetric::getWeeklySummary($lastWeekStart);

        // Weekly trends
        $trends = $this->getWeeklyTrends(4);

        // Current week goals
        $currentGoals = WeeklyGoal::currentWeek()->get();
        $completedGoals = $currentGoals->where('status', 'completed')->count();
        $totalGoals = $currentGoals->count();
        $goalCompletionRate = $totalGoals > 0 ? round(($completedGoals / $totalGoals) * 100, 2) : 0;

        // Calculate week-over-week change
        $weekChange = 0;
        if ($lastWeekMetrics['total_points'] > 0) {
            $weekChange = round((($thisWeekMetrics['total_points'] - $lastWeekMetrics['total_points']) / $lastWeekMetrics['total_points']) * 100, 2);
        }

        return [
            'today' => [
                'score' => $todayMetrics?->productivity_score ?? 0,
                'level' => $todayMetrics?->getProductivityLevel() ?? 'none',
                'points' => $todayMetrics?->total_points ?? 0,
                'study_minutes' => $todayMetrics?->total_study_minutes ?? 0,
            ],
            'this_week' => $thisWeekMetrics,
            'last_week' => $lastWeekMetrics,
            'week_change' => $weekChange,
            'goals' => [
                'completed' => $completedGoals,
                'total' => $totalGoals,
                'completion_rate' => $goalCompletionRate,
            ],
            'trends' => $trends,
        ];
    }

    /**
     * Get productivity statistics for a date range
     */
    public function getDateRangeStats(Carbon $startDate, Carbon $endDate): array
    {
        $metrics = ProductivityMetric::whereBetween('metric_date', [$startDate, $endDate])->get();

        if ($metrics->isEmpty()) {
            return [
                'total_points' => 0,
                'total_study_minutes' => 0,
                'total_deep_work_minutes' => 0,
                'avg_daily_score' => 0,
                'total_tasks' => 0,
                'total_references' => 0,
                'total_tutorials' => 0,
                'total_vocabulary' => 0,
                'total_notes' => 0,
                'days_active' => 0,
            ];
        }

        return [
            'total_points' => $metrics->sum('total_points'),
            'total_study_minutes' => $metrics->sum('total_study_minutes'),
            'total_deep_work_minutes' => $metrics->sum('deep_work_minutes'),
            'avg_daily_score' => round($metrics->avg('productivity_score'), 2),
            'total_tasks' => $metrics->sum('tasks_completed'),
            'total_references' => $metrics->sum('references_reviewed'),
            'total_tutorials' => $metrics->sum('tutorials_completed'),
            'total_vocabulary' => $metrics->sum('vocabulary_learned'),
            'total_notes' => $metrics->sum('notes_created'),
            'days_active' => $metrics->count(),
            'best_day' => $metrics->sortByDesc('productivity_score')->first(),
            'worst_day' => $metrics->sortBy('productivity_score')->first(),
        ];
    }

    /**
     * Get activity breakdown for a date range
     */
    public function getActivityBreakdown(Carbon $startDate, Carbon $endDate): array
    {
        $sessions = ProductivitySession::whereBetween('session_date', [$startDate, $endDate])
            ->selectRaw('
                activity_type,
                module_source,
                count(*) as count,
                sum(duration_minutes) as total_minutes,
                sum(points_earned) as total_points
            ')
            ->groupBy('activity_type', 'module_source')
            ->get();

        return $sessions->groupBy('module_source')->map(function ($activities) {
            return [
                'total_sessions' => $activities->sum('count'),
                'total_minutes' => $activities->sum('total_minutes'),
                'total_points' => $activities->sum('total_points'),
                'activities' => $activities->keyBy('activity_type')->map(function ($activity) {
                    return [
                        'count' => $activity->count,
                        'minutes' => $activity->total_minutes,
                        'points' => $activity->total_points,
                    ];
                }),
            ];
        })->toArray();
    }

    /**
     * Get productivity level based on weekly score
     */
    public function getWeeklyLevel(int $totalPoints): string
    {
        if ($totalPoints >= 1500) return 'scholar';
        if ($totalPoints >= 1000) return 'researcher';
        if ($totalPoints >= 700) return 'academic';
        if ($totalPoints >= 500) return 'student';
        if ($totalPoints >= 300) return 'learner';
        return 'beginner';
    }

    /**
     * Calculate streak (consecutive days with activity)
     */
    public function calculateStreak(): int
    {
        $streak = 0;
        $date = Carbon::now();

        while (true) {
            $metric = ProductivityMetric::where('metric_date', $date->format('Y-m-d'))->first();
            
            if (!$metric || $metric->total_points === 0) {
                break;
            }

            $streak++;
            $date->subDay();

            // Safety limit
            if ($streak > 365) break;
        }

        return $streak;
    }

    /**
     * Log manual study session
     */
    public function logManualSession(array $data): ProductivitySession
    {
        $session = ProductivitySession::create([
            'user_id' => auth()->id(),
            'weekly_goal_id' => $data['weekly_goal_id'] ?? null,
            'activity_type' => $data['activity_type'],
            'module_source' => $data['module_source'] ?? 'manual',
            'duration_minutes' => $data['duration_minutes'],
            'points_earned' => $this->calculateManualPoints($data['activity_type'], $data['duration_minutes']),
            'description' => $data['description'] ?? 'Manual study session',
            'session_date' => now(),
        ]);

        // Update daily metrics
        $this->aggregateDailyMetrics();

        return $session;
    }

    private function calculateManualPoints(string $activityType, int $minutes): int
    {
        $pointsPerHour = [
            'deep_work' => 6,
            'reading' => 10,
            'writing' => 12,
            'coding' => 10,
            'review' => 5,
        ];

        $rate = $pointsPerHour[$activityType] ?? 5;
        return round(($minutes / 60) * $rate);
    }
}
