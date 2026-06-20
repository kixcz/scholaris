<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeeklyGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_id',
        'milestone_id',
        'roadmap_id',
        'title',
        'description',
        'category',
        'urgency',
        'status',
        'week_start_date',
        'week_end_date',
        'completion_rate',
        'targets',
        'achievements',
        'actual_hours',
        'auto_generated',
    ];

    protected $casts = [
        'week_start_date' => 'date',
        'week_end_date' => 'date',
        'completion_rate' => 'decimal:2',
        'targets' => 'array',
        'achievements' => 'array',
        'auto_generated' => 'boolean',
        'actual_hours' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope('user', function ($query) {
            if (auth()->check()) {
                $query->where('user_id', auth()->id());
            }
        });

        // Auto-update completion rate and status
        static::saving(function ($goal) {
            $goal->completion_rate = $goal->calculateCompletionRate();
            
            if ($goal->completion_rate >= 100 && !in_array($goal->status, ['completed', 'failed'])) {
                $goal->status = 'completed';
                
                // Award bonus points for completion
                ProductivitySession::create([
                    'user_id' => auth()->id(),
                    'weekly_goal_id' => $goal->id,
                    'activity_type' => 'goal_completion',
                    'module_source' => 'weekly_goals',
                    'duration_minutes' => 0,
                    'points_earned' => 50,
                    'description' => 'Weekly goal completed: ' . $goal->title,
                    'session_date' => now(),
                ]);
            } elseif ($goal->completion_rate > 0 && $goal->status === 'planned') {
                $goal->status = 'in_progress';
            }
        });

        // When goal completes, update milestone
        static::updated(function ($goal) {
            if ($goal->status === 'completed' && $goal->milestone_id) {
                $milestone = $goal->milestone;
                if ($milestone) {
                    $milestone->updateProgressFromGoals();
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WeeklyGoalTemplate::class, 'template_id');
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function roadmap(): BelongsTo
    {
        return $this->belongsTo(Roadmap::class);
    }

    public function productivitySessions(): HasMany
    {
        return $this->hasMany(ProductivitySession::class);
    }

    // Calculate completion rate based on targets vs achievements
    public function calculateCompletionRate(): float
    {
        if (!$this->targets || empty($this->targets)) {
            return $this->completion_rate ?? 0;
        }
        
        $totalTargets = count($this->targets);
        $completedTargets = 0;

        foreach ($this->targets as $key => $target) {
            $achieved = $this->achievements[$key] ?? 0;
            if ($achieved >= $target) {
                $completedTargets++;
            }
        }

        return round(($completedTargets / $totalTargets) * 100, 2);
    }

    // Log activity and update achievements
    public function logActivity(string $activityType, int $amount = 1, int $durationMinutes = 0): void
    {
        $achievements = $this->achievements ?? [];
        $achievements[$activityType] = ($achievements[$activityType] ?? 0) + $amount;
        
        $this->update(['achievements' => $achievements]);

        // Calculate points
        $points = $this->calculatePoints($activityType, $amount);

        // Estimate duration if not provided
        if ($durationMinutes === 0) {
            $durationMinutes = $this->estimateDuration($activityType, $amount);
        }

        // Log productivity session
        ProductivitySession::create([
            'user_id' => auth()->id(),
            'weekly_goal_id' => $this->id,
            'activity_type' => $activityType,
            'module_source' => $this->getModuleSource($activityType),
            'duration_minutes' => $durationMinutes,
            'points_earned' => $points,
            'description' => "Completed: {$amount} {$activityType}",
            'session_date' => now(),
        ]);
    }

    private function getModuleSource(string $activityType): string
    {
        $map = [
            'papers_read' => 'references',
            'papers_collected' => 'references',
            'summaries_written' => 'notes',
            'vocabulary_learned' => 'vocabulary',
            'vocabulary_reviewed' => 'vocabulary',
            'tutorials_completed' => 'tutorials',
            'tasks_completed' => 'tasks',
            'notes_created' => 'notes',
        ];
        return $map[$activityType] ?? 'general';
    }

    private function estimateDuration(string $activityType, int $amount): int
    {
        $durations = [
            'papers_read' => 30,
            'papers_collected' => 5,
            'summaries_written' => 45,
            'vocabulary_learned' => 5,
            'vocabulary_reviewed' => 2,
            'tutorials_completed' => 60,
            'tasks_completed' => 15,
            'notes_created' => 20,
        ];
        return ($durations[$activityType] ?? 10) * $amount;
    }

    private function calculatePoints(string $activityType, int $amount): int
    {
        $points = [
            'papers_read' => 20,
            'papers_collected' => 5,
            'summaries_written' => 25,
            'vocabulary_learned' => 5,
            'vocabulary_reviewed' => 2,
            'tutorials_completed' => 30,
            'tasks_completed' => 10,
            'notes_created' => 8,
        ];
        return ($points[$activityType] ?? 5) * $amount;
    }

    // Get completion percentage for specific target
    public function getTargetProgress(string $targetKey): array
    {
        $target = $this->targets[$targetKey] ?? 0;
        $achieved = $this->achievements[$targetKey] ?? 0;
        $percentage = $target > 0 ? min(100, round(($achieved / $target) * 100, 2)) : 0;
        
        return [
            'target' => $target,
            'achieved' => $achieved,
            'percentage' => $percentage,
        ];
    }

    // Scopes
    public function scopeCurrentWeek($query)
    {
        return $query->where('week_start_date', '<=', now())
                     ->where('week_end_date', '>=', now());
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['planned', 'in_progress']);
    }

    public function scopeAutoGenerated($query)
    {
        return $query->where('auto_generated', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForRoadmap($query, $roadmapId)
    {
        return $query->where('roadmap_id', $roadmapId);
    }

    public function scopeForMilestone($query, $milestoneId)
    {
        return $query->where('milestone_id', $milestoneId);
    }
}
