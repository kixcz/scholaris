<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ProductivityMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'weekly_goal_id',
        'metric_date',
        'tasks_completed',
        'references_reviewed',
        'tutorials_completed',
        'vocabulary_learned',
        'notes_created',
        'deep_work_minutes',
        'total_study_minutes',
        'total_points',
        'productivity_score',
        'breakdown',
    ];

    protected $casts = [
        'metric_date' => 'date',
        'breakdown' => 'array',
        'productivity_score' => 'decimal:2',
        'total_study_minutes' => 'integer',
        'deep_work_minutes' => 'integer',
        'total_points' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope('user', function ($query) {
            if (auth()->check()) {
                $query->where('user_id', auth()->id());
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function weeklyGoal(): BelongsTo
    {
        return $this->belongsTo(WeeklyGoal::class);
    }

    // Recalculate metrics from sessions
    public function recalculateFromSessions(): void
    {
        $sessions = ProductivitySession::where('user_id', $this->user_id)
            ->whereDate('session_date', $this->metric_date)
            ->get();

        $this->update([
            'tasks_completed' => $sessions->where('activity_type', 'task')->count(),
            'references_reviewed' => $sessions->where('activity_type', 'papers_read')->count(),
            'tutorials_completed' => $sessions->where('activity_type', 'tutorial')->count(),
            'vocabulary_learned' => $sessions->where('activity_type', 'vocabulary')->count(),
            'notes_created' => $sessions->where('activity_type', 'note')->count(),
            'deep_work_minutes' => $sessions->where('activity_type', 'deep_work')->sum('duration_minutes'),
            'total_study_minutes' => $sessions->sum('duration_minutes'),
            'total_points' => $sessions->sum('points_earned'),
            'breakdown' => [
                'sessions_count' => $sessions->count(),
                'activity_types' => $sessions->groupBy('activity_type')->map->count()->toArray(),
            ],
        ]);

        $this->calculateScore();
    }

    // Calculate daily productivity score
    public function calculateScore(): float
    {
        $score = 0;
        
        // Weighted scoring
        $score += $this->tasks_completed * 10;
        $score += $this->references_reviewed * 20;
        $score += $this->tutorials_completed * 30;
        $score += $this->vocabulary_learned * 5;
        $score += $this->notes_created * 8;
        $score += ($this->deep_work_minutes / 60) * 15; // 15 points per hour of deep work
        
        // Bonus for total study time
        if ($this->total_study_minutes > 120) {
            $score += 20; // Bonus for 2+ hours
        }
        if ($this->total_study_minutes > 240) {
            $score += 30; // Bonus for 4+ hours
        }
        if ($this->total_study_minutes > 360) {
            $score += 50; // Bonus for 6+ hours
        }

        $this->productivity_score = round($score, 2);
        $this->save();
        
        return $score;
    }

    // Get weekly summary
    public static function getWeeklySummary(Carbon $weekStart): array
    {
        $weekEnd = $weekStart->copy()->addDays(6);
        
        $metrics = self::whereBetween('metric_date', [$weekStart, $weekEnd])->get();

        if ($metrics->isEmpty()) {
            return [
                'total_tasks' => 0,
                'total_references' => 0,
                'total_tutorials' => 0,
                'total_vocabulary' => 0,
                'total_notes' => 0,
                'total_deep_work_minutes' => 0,
                'total_study_minutes' => 0,
                'total_points' => 0,
                'avg_daily_score' => 0,
                'days_active' => 0,
            ];
        }

        return [
            'total_tasks' => $metrics->sum('tasks_completed'),
            'total_references' => $metrics->sum('references_reviewed'),
            'total_tutorials' => $metrics->sum('tutorials_completed'),
            'total_vocabulary' => $metrics->sum('vocabulary_learned'),
            'total_notes' => $metrics->sum('notes_created'),
            'total_deep_work_minutes' => $metrics->sum('deep_work_minutes'),
            'total_study_minutes' => $metrics->sum('total_study_minutes'),
            'total_points' => $metrics->sum('total_points'),
            'avg_daily_score' => round($metrics->avg('productivity_score'), 2),
            'days_active' => $metrics->count(),
        ];
    }

    // Get trends for last N weeks
    public static function getWeeklyTrends(int $weeks = 4): array
    {
        $trends = [];
        
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
            $summary = self::getWeeklySummary($weekStart);
            $summary['week_start'] = $weekStart->format('Y-m-d');
            $summary['week_label'] = $weekStart->format('M d');
            $trends[] = $summary;
        }
        
        return $trends;
    }

    // Get today's metrics
    public static function getTodayMetrics(): ?self
    {
        return self::where('metric_date', now()->format('Y-m-d'))->first();
    }

    // Get this week's metrics
    public static function getThisWeekMetrics(): array
    {
        return self::getWeeklySummary(now()->startOfWeek());
    }

    // Get productivity level based on score
    public function getProductivityLevel(): string
    {
        if ($this->productivity_score >= 200) return 'exceptional';
        if ($this->productivity_score >= 150) return 'excellent';
        if ($this->productivity_score >= 100) return 'good';
        if ($this->productivity_score >= 50) return 'moderate';
        return 'light';
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->where('metric_date', now()->format('Y-m-d'));
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('metric_date', [
            now()->startOfWeek()->format('Y-m-d'),
            now()->endOfWeek()->format('Y-m-d'),
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('metric_date', now()->month)
                     ->whereYear('metric_date', now()->year);
    }

    public function scopeLastNDays($query, int $days)
    {
        return $query->where('metric_date', '>=', now()->subDays($days)->format('Y-m-d'));
    }
}
