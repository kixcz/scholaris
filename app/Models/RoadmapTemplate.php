<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoadmapTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'phases',
        'success_criteria',
        'estimated_duration_weeks',
        'linked_modules',
        'is_public',
    ];

    protected $casts = [
        'phases' => 'array',
        'success_criteria' => 'array',
        'linked_modules' => 'array',
        'estimated_duration_weeks' => 'integer',
        'is_public' => 'boolean',
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

    public function roadmaps(): HasMany
    {
        return $this->hasMany(Roadmap::class, 'template_id');
    }

    // Helper method to create roadmap from template
    public function createRoadmap(array $data): Roadmap
    {
        $roadmapData = array_merge([
            'title' => $this->name,
            'description' => $this->description,
            'target_end_date' => now()->addWeeks($this->estimated_duration_weeks ?? 12),
            'linked_modules' => $this->phases ? $this->extractLinkedModules() : null,
        ], $data);

        $roadmap = $this->roadmaps()->create($roadmapData);

        // Create milestones from phases
        if ($this->phases) {
            $this->createMilestonesFromPhases($roadmap);
        }

        return $roadmap;
    }

    private function extractLinkedModules(): array
    {
        $modules = [];
        foreach ($this->phases as $phase) {
            if (isset($phase['linked_modules'])) {
                $modules = array_merge($modules, $phase['linked_modules']);
            }
        }
        return array_unique($modules);
    }

    private function createMilestonesFromPhases(Roadmap $roadmap): void
    {
        $sequence = 1;
        foreach ($this->phases as $phaseNumber => $phase) {
            foreach ($phase['milestones'] ?? [] as $milestoneData) {
                $roadmap->milestones()->create([
                    'phase_number' => $phaseNumber + 1,
                    'sequence_order' => $sequence++,
                    'title' => $milestoneData['title'],
                    'description' => $milestoneData['description'] ?? null,
                    'target_date' => $this->calculateTargetDate($phaseNumber, $milestoneData['week_offset'] ?? 0),
                    'expected_outcomes' => $milestoneData['expected_outcomes'] ?? null,
                    'success_metrics' => $milestoneData['success_metrics'] ?? null,
                    'linked_module' => $milestoneData['linked_module'] ?? null,
                    'linked_goal_id' => $milestoneData['linked_goal_template_id'] ?? null,
                ]);
            }
        }
    }

    private function calculateTargetDate(int $phaseNumber, int $weekOffset): \DateTime
    {
        $weeks = ($phaseNumber - 1) * 4 + $weekOffset; // Assume 4 weeks per phase
        return now()->addWeeks($weeks);
    }
}
