<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Roadmap extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_id',
        'title',
        'description',
        'status',
        'start_date',
        'target_end_date',
        'actual_end_date',
        'color',
        'current_phase',
        'overall_progress',
        'linked_modules',
    ];

    protected $casts = [
        'start_date' => 'date',
        'target_end_date' => 'date',
        'actual_end_date' => 'date',
        'overall_progress' => 'decimal:2',
        'linked_modules' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope('user', function ($query) {
            if (auth()->check()) {
                $query->where('user_id', auth()->id());
            }
        });

        // Auto-update progress when milestones change
        static::saving(function ($roadmap) {
            $roadmap->overall_progress = $roadmap->calculateProgress();
            
            // Auto-update status based on progress
            if ($roadmap->overall_progress >= 100 && !$roadmap->actual_end_date) {
                $roadmap->status = 'completed';
                $roadmap->actual_end_date = now();
            } elseif ($roadmap->overall_progress > 0 && $roadmap->status === 'planned') {
                $roadmap->status = 'active';
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(RoadmapTemplate::class, 'template_id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function weeklyGoals(): HasMany
    {
        return $this->hasMany(WeeklyGoal::class);
    }

    // Calculate overall progress from milestones
    public function calculateProgress(): float
    {
        $total = $this->milestones()->count();
        if ($total === 0) return 0;
        
        $completed = $this->milestones()->where('status', 'completed')->count();
        return round(($completed / $total) * 100, 2);
    }

    // Get current active milestones
    public function getCurrentMilestones()
    {
        return $this->milestones()
            ->where('phase_number', $this->current_phase)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('sequence_order')
            ->get();
    }

    // Auto-generate weekly goals from current milestones
    public function generateWeeklyGoals(\Carbon\Carbon $weekStart, \Carbon\Carbon $weekEnd)
    {
        $currentMilestones = $this->getCurrentMilestones();
        
        foreach ($currentMilestones as $milestone) {
            if ($milestone->linked_goal_id) {
                $template = WeeklyGoalTemplate::find($milestone->linked_goal_id);
                if ($template) {
                    $template->createWeeklyGoal([
                        'milestone_id' => $milestone->id,
                        'roadmap_id' => $this->id,
                        'week_start_date' => $weekStart,
                        'week_end_date' => $weekEnd,
                        'auto_generated' => true,
                    ]);
                }
            }
        }
    }

    // Get all contributing module statistics
    public function getModuleStats(): array
    {
        $stats = [];
        
        if (in_array('references', $this->linked_modules ?? [])) {
            $stats['references'] = Reference::whereHas('notes', function($q) {
                $q->where('roadmap_id', $this->id);
            })->count();
        }
        
        // Add more module stats as needed
        
        return $stats;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->whereHas('template', function($q) use ($category) {
            $q->where('category', $category);
        });
    }
}
