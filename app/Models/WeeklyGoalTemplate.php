<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeeklyGoalTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'default_targets',
        'estimated_hours',
        'is_active',
    ];

    protected $casts = [
        'default_targets' => 'array',
        'is_active' => 'boolean',
        'estimated_hours' => 'integer',
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

    public function weeklyGoals(): HasMany
    {
        return $this->hasMany(WeeklyGoal::class, 'template_id');
    }

    // Create weekly goal instance from template
    public function createWeeklyGoal(array $additionalData = []): WeeklyGoal
    {
        $weekStart = $additionalData['week_start_date'] ?? now()->startOfWeek();
        $weekEnd = $additionalData['week_end_date'] ?? now()->endOfWeek();

        return $this->weeklyGoals()->create(array_merge([
            'title' => $this->name . ' - Week of ' . $weekStart->format('M d'),
            'description' => $this->description,
            'category' => $this->category,
            'targets' => $this->default_targets,
            'achievements' => array_map(function() { return 0; }, $this->default_targets ?? []),
            'week_start_date' => $weekStart,
            'week_end_date' => $weekEnd,
        ], $additionalData));
    }

    // Get common templates
    public static function getCommonTemplates(): array
    {
        return [
            [
                'name' => 'Research Reading Sprint',
                'description' => 'Focus on literature review and paper analysis',
                'category' => 'research',
                'default_targets' => [
                    'papers_to_read' => 5,
                    'summaries_to_write' => 3,
                    'vocabulary_terms' => 10,
                    'research_notes' => 2,
                ],
                'estimated_hours' => 15,
            ],
            [
                'name' => 'Academic Writing Sprint',
                'description' => 'Intensive writing period for papers or thesis',
                'category' => 'writing',
                'default_targets' => [
                    'words_to_write' => 2000,
                    'citations_to_add' => 10,
                    'sections_to_draft' => 1,
                    'revisions' => 2,
                ],
                'estimated_hours' => 20,
            ],
            [
                'name' => 'Thesis Development Sprint',
                'description' => 'Work on thesis chapters and research',
                'category' => 'thesis',
                'default_targets' => [
                    'chapters_to_work_on' => 1,
                    'references_to_collect' => 5,
                    'data_analysis_tasks' => 3,
                    'advisor_meetings' => 1,
                ],
                'estimated_hours' => 25,
            ],
            [
                'name' => 'Skill Development Sprint',
                'description' => 'Learn new technical skills through tutorials',
                'category' => 'skill',
                'default_targets' => [
                    'tutorials_to_complete' => 2,
                    'practice_exercises' => 5,
                    'vocabulary_terms' => 15,
                    'project_tasks' => 10,
                ],
                'estimated_hours' => 12,
            ],
            [
                'name' => 'Project Development Sprint',
                'description' => 'Build and iterate on research projects',
                'category' => 'project',
                'default_targets' => [
                    'features_to_implement' => 1,
                    'bugs_to_fix' => 5,
                    'tests_to_write' => 10,
                    'documentation_pages' => 2,
                ],
                'estimated_hours' => 18,
            ],
        ];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
