<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductivitySession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'weekly_goal_id',
        'task_id',
        'activity_type',
        'module_source',
        'duration_minutes',
        'points_earned',
        'description',
        'metadata',
        'session_date',
    ];

    protected $casts = [
        'metadata' => 'array',
        'session_date' => 'datetime',
        'duration_minutes' => 'integer',
        'points_earned' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope('user', function ($query) {
            if (auth()->check()) {
                $query->where('user_id', auth()->id());
            }
        });

        // Update daily metrics when session is created
        static::created(function ($session) {
            ProductivityMetric::updateOrCreate(
                [
                    'user_id' => $session->user_id,
                    'metric_date' => $session->session_date->format('Y-m-d'),
                ],
                []
            )->recalculateFromSessions();
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

    // Get sessions by date range
    public static function getByDateRange($startDate, $endDate, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        return self::where('user_id', $userId)
            ->whereBetween('session_date', [$startDate, $endDate])
            ->orderBy('session_date', 'desc')
            ->get();
    }

    // Get total points for date range
    public static function getTotalPoints($startDate, $endDate)
    {
        return self::whereBetween('session_date', [$startDate, $endDate])
            ->sum('points_earned');
    }

    // Get total study time for date range
    public static function getTotalStudyTime($startDate, $endDate)
    {
        return self::whereBetween('session_date', [$startDate, $endDate])
            ->sum('duration_minutes');
    }

    // Get activity breakdown
    public static function getActivityBreakdown($startDate, $endDate)
    {
        return self::whereBetween('session_date', [$startDate, $endDate])
            ->selectRaw('activity_type, count(*) as count, sum(duration_minutes) as total_minutes, sum(points_earned) as total_points')
            ->groupBy('activity_type')
            ->get();
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('session_date', now());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('session_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('session_date', now()->month)
                     ->whereYear('session_date', now()->year);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('module_source', $module);
    }
}
