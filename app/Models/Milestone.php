<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'roadmap_id',
        'title',
        'description',
        'phase_number',
        'sequence_order',
        'target_date',
        'completed_at',
        'status',
        'progress',
        'expected_outcomes',
        'success_metrics',
        'linked_module',
        'linked_goal_id',
    ];

    protected $casts = [
        'target_date' => 'date',
        'completed_at' => 'date',
        'progress' => 'decimal:2',
        'expected_outcomes' => 'array',
        'success_metrics' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-complete when progress reaches 100%
        static::updating(function ($milestone) {
            if ($milestone->progress >= 100 && $milestone->status !== 'completed') {
                $milestone->status = 'completed';
                $milestone->completed_at = now();
                
                // Update roadmap progress
                $milestone->roadmap->update(['overall_progress' => $milestone->roadmap->calculateProgress()]);
            }
        });
    }

    public function roadmap(): BelongsTo
    {
        return $this->belongsTo(Roadmap::class);
    }

    public function weeklyGoalTemplate(): BelongsTo
    {
        return $this->belongsTo(WeeklyGoalTemplate::class, 'linked_goal_id');
    }

    public function weeklyGoals(): HasMany
    {
        return $this->hasMany(WeeklyGoal::class);
    }

    // Update progress based on linked weekly goals
    public function updateProgressFromGoals(): void
    {
        $goals = $this->weeklyGoals()->get();
        
        if ($goals->isEmpty()) {
            $this->update(['progress' => 0]);
            return;
        }

        $avgCompletion = $goals->avg('completion_rate') ?? 0;
        $this->update(['progress' => $avgCompletion]);
    }

    // Update progress from linked module activity
    public function updateProgressFromModule(): void
    {
        if (!$this->linked_module) return;

        $progress = match ($this->linked_module) {
            'references' => $this->calculateReferenceProgress(),
            'vocabulary' => $this->calculateVocabularyProgress(),
            'notes' => $this->calculateNotesProgress(),
            'tutorials' => $this->calculateTutorialsProgress(),
            default => 0,
        };

        $this->update(['progress' => $progress]);
    }

    private function calculateReferenceProgress(): float
    {
        $metrics = $this->success_metrics ?? [];
        $target = $metrics['papers_read'] ?? $metrics['papers_collected'] ?? 0;
        
        if ($target === 0) return 0;

        $user = auth()->user();
        $completed = $user->references()->where('status', 'completed')->count();
        
        return min(100, round(($completed / $target) * 100, 2));
    }

    private function calculateVocabularyProgress(): float
    {
        $metrics = $this->success_metrics ?? [];
        $target = $metrics['terms_learned'] ?? $metrics['vocabulary'] ?? 0;
        
        if ($target === 0) return 0;

        $user = auth()->user();
        $completed = $user->vocabulary()->count();
        
        return min(100, round(($completed / $target) * 100, 2));
    }

    private function calculateNotesProgress(): float
    {
        $metrics = $this->success_metrics ?? [];
        $target = $metrics['notes_created'] ?? $metrics['summaries'] ?? 0;
        
        if ($target === 0) return 0;

        $user = auth()->user();
        $completed = $user->notes()->count();
        
        return min(100, round(($completed / $target) * 100, 2));
    }

    private function calculateTutorialsProgress(): float
    {
        // Placeholder - implement when tutorials module exists
        return 0;
    }

    // Scopes
    public function scopeCurrentPhase($query, $phaseNumber)
    {
        return $query->where('phase_number', $phaseNumber);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('target_date', '<', now())
                     ->whereIn('status', ['pending', 'in_progress']);
    }
}
