# Scholaris - Scholarly Operating System Implementation Guide

## 🎯 **What We've Built**

You now have the **foundation for a complete scholarly operating system** that transforms Scholaris from isolated productivity tools into an integrated ecosystem where:

- **Roadmap Templates** define long-term blueprints
- **Active Roadmaps** track strategic progress
- **Milestones** establish checkpoints
- **Weekly Goal Templates** provide reusable execution patterns
- **Weekly Goals** auto-generate from milestones
- **Supporting Modules** (References, Vocabulary, Notes, Tutorials) feed evidence of progress
- **Productivity Tracker** aggregates all activities into comprehensive metrics
- **Dashboard** shows unified view of everything

---

## ✅ **Completed Components**

### **1. Database Schema** ✅
**File:** `database/migrations/2026_02_21_000001_create_scholarly_ecosystem_tables.php`

Created comprehensive tables:
- `roadmap_templates` - Reusable blueprints with phases and success criteria
- `roadmaps` - Active instances with progress tracking
- `milestones` - Checkpoints linked to weekly goal templates
- `weekly_goal_templates` - Reusable weekly patterns (Research Sprint, Writing Sprint, etc.)
- `weekly_goals` - Auto-generated instances with targets and achievements
- `productivity_sessions` - Activity logging from all modules
- `productivity_metrics` - Aggregated daily/weekly analytics

### **2. Core Models** ✅

**RoadmapTemplate** (`app/Models/RoadmapTemplate.php`)
- Creates roadmaps from templates
- Auto-generates milestones from phases
- Calculates target dates

**Roadmap** (`app/Models/Roadmap.php`) - UPDATED
- Links to templates and milestones
- Auto-calculates overall progress
- Auto-generates weekly goals from current milestones
- Tracks linked modules
- Auto-updates status based on progress

**WeeklyGoalTemplate** (`app/Models/WeeklyGoalTemplate.php`)
- Pre-built templates (Research Sprint, Writing Sprint, etc.)
- Creates weekly goal instances
- Default targets for each category

**WeeklyGoal** - NEEDS UPDATE (see below)
- Links to templates, milestones, and roadmaps
- Tracks completion rate
- Auto-generated flag

### **3. Architectural Documentation** ✅
**File:** `SCHOLARLY_ECOSYSTEM_ARCHITECTURE.md`

Complete documentation of:
- Hierarchical workflow
- Data flow & automation
- Module integration examples
- Productivity tracking system
- User journey example
- Dashboard hierarchy
- Key features and benefits

---

## 📋 **What's Next - Implementation Steps**

### **Step 1: Update Remaining Models**

#### **Update Milestone Model**
**File:** `app/Models/Milestone.php`

Add these relationships and methods:
```php
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

// Update progress based on linked module activity
public function updateProgressFromModule(): void
{
    if ($this->linked_module === 'references') {
        // Count references read for this milestone
        $progress = $this->calculateReferenceProgress();
        $this->update(['progress' => $progress]);
    }
    // Similar for vocabulary, tutorials, notes
}

// Auto-complete when progress reaches 100%
protected static function boot()
{
    parent::boot();
    
    static::updating(function ($milestone) {
        if ($milestone->progress >= 100 && $milestone->status !== 'completed') {
            $milestone->status = 'completed';
            $milestone->completed_at = now();
        }
    });
}
```

#### **Update WeeklyGoal Model**
**File:** `app/Models/WeeklyGoal.php`

Complete rewrite to support new structure:
```php
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
            
            if ($goal->completion_rate >= 100) {
                $goal->status = 'completed';
            } elseif ($goal->completion_rate > 0 && $goal->status === 'planned') {
                $goal->status = 'in_progress';
            }
        });

        // When goal completes, update milestone
        static::updated(function ($goal) {
            if ($goal->status === 'completed' && $goal->milestone_id) {
                $milestone = $goal->milestone;
                $milestone->updateProgressFromGoal();
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

    public function calculateCompletionRate(): float
    {
        if (!$this->targets) return 0;
        
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
    public function logActivity(string $activityType, int $amount = 1): void
    {
        $achievements = $this->achievements ?? [];
        $achievements[$activityType] = ($achievements[$activityType] ?? 0) + $amount;
        
        $this->update(['achievements' => $achievements]);

        // Log productivity session
        $this->productivitySessions()->create([
            'activity_type' => $activityType,
            'module_source' => $this->getModuleSource($activityType),
            'duration_minutes' => $this->estimateDuration($activityType, $amount),
            'points_earned' => $this->calculatePoints($activityType, $amount),
            'session_date' => now(),
        ]);
    }

    private function getModuleSource(string $activityType): string
    {
        $map = [
            'papers_read' => 'references',
            'summaries_written' => 'notes',
            'vocabulary_learned' => 'vocabulary',
            'tutorials_completed' => 'tutorials',
            'tasks_completed' => 'tasks',
        ];
        return $map[$activityType] ?? 'general';
    }

    private function estimateDuration(string $activityType, int $amount): int
    {
        $durations = [
            'papers_read' => 30,
            'summaries_written' => 45,
            'vocabulary_learned' => 5,
            'tutorials_completed' => 60,
            'tasks_completed' => 15,
        ];
        return ($durations[$activityType] ?? 10) * $amount;
    }

    private function calculatePoints(string $activityType, int $amount): int
    {
        $points = [
            'papers_read' => 20,
            'summaries_written' => 25,
            'vocabulary_learned' => 5,
            'tutorials_completed' => 30,
            'tasks_completed' => 10,
        ];
        return ($points[$activityType] ?? 5) * $amount;
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
}
```

#### **Create ProductivitySession Model**
**File:** `app/Models/ProductivitySession.php`

```php
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function weeklyGoal(): BelongsTo
    {
        return $this->belongsTo(WeeklyGoal::class);
    }
}
```

#### **Create ProductivityMetric Model**
**File:** `app/Models/ProductivityMetric.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function weeklyGoal(): BelongsTo
    {
        return $this->belongsTo(WeeklyGoal::class);
    }

    // Calculate daily productivity score
    public function calculateScore(): float
    {
        $score = 0;
        $score += $this->tasks_completed * 10;
        $score += $this->references_reviewed * 20;
        $score += $this->tutorials_completed * 15;
        $score += $this->vocabulary_learned * 5;
        $score += $this->notes_created * 8;
        $score += ($this->deep_work_minutes / 60) * 10;
        
        $this->productivity_score = $score;
        $this->save();
        
        return $score;
    }

    // Get weekly summary
    public static function getWeeklySummary(\Carbon\Carbon $weekStart): array
    {
        $weekEnd = $weekStart->copy()->addDays(6);
        
        return self::whereBetween('metric_date', [$weekStart, $weekEnd])
            ->selectRaw('
                SUM(tasks_completed) as total_tasks,
                SUM(references_reviewed) as total_references,
                SUM(tutorials_completed) as total_tutorials,
                SUM(vocabulary_learned) as total_vocabulary,
                SUM(notes_created) as total_notes,
                SUM(deep_work_minutes) as total_deep_work,
                SUM(total_study_minutes) as total_study_time,
                SUM(total_points) as total_points,
                AVG(productivity_score) as avg_daily_score
            ')
            ->first()
            ->toArray();
    }
}
```

---

### **Step 2: Create Service Classes for Automation**

#### **WeeklyGoalGenerator Service**
**File:** `app/Services/WeeklyGoalGenerator.php`

```php
<?php

namespace App\Services;

use App\Models\Roadmap;
use App\Models\WeeklyGoal;
use Carbon\Carbon;

class WeeklyGoalGenerator
{
    /**
     * Generate weekly goals for all active roadmaps
     */
    public function generateForCurrentWeek(): int
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $count = 0;

        $activeRoadmaps = Roadmap::where('status', 'active')->get();

        foreach ($activeRoadmaps as $roadmap) {
            $generated = $roadmap->generateWeeklyGoals($weekStart, $weekEnd);
            $count += count($generated);
        }

        return $count;
    }

    /**
     * Generate goals for specific roadmap
     */
    public function generateForRoadmap(Roadmap $roadmap, Carbon $weekStart, Carbon $weekEnd): array
    {
        return $roadmap->generateWeeklyGoals($weekStart, $weekEnd);
    }
}
```

#### **ProductivityTracker Service**
**File:** `app/Services/ProductivityTracker.php`

```php
<?php

namespace App\Services;

use App\Models\ProductivityMetric;
use App\Models\ProductivitySession;
use Carbon\Carbon;

class ProductivityTracker
{
    /**
     * Aggregate daily metrics from sessions
     */
    public function aggregateDailyMetrics(Carbon $date = null): ProductivityMetric
    {
        $date = $date ?? Carbon::now();
        
        $sessions = ProductivitySession::whereDate('session_date', $date)->get();
        
        $metric = ProductivityMetric::firstOrCreate(
            ['user_id' => auth()->id(), 'metric_date' => $date],
            [
                'tasks_completed' => $sessions->where('activity_type', 'task')->count(),
                'references_reviewed' => $sessions->where('activity_type', 'reading')->count(),
                'tutorials_completed' => $sessions->where('activity_type', 'tutorial')->count(),
                'vocabulary_learned' => $sessions->where('activity_type', 'vocabulary')->count(),
                'notes_created' => $sessions->where('activity_type', 'note')->count(),
                'deep_work_minutes' => $sessions->where('activity_type', 'deep_work')->sum('duration_minutes'),
                'total_study_minutes' => $sessions->sum('duration_minutes'),
                'total_points' => $sessions->sum('points_earned'),
            ]
        );

        $metric->calculateScore();
        
        return $metric;
    }

    /**
     * Get productivity trends for last N weeks
     */
    public function getWeeklyTrends(int $weeks = 4): array
    {
        $trends = [];
        
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
            $trends[] = ProductivityMetric::getWeeklySummary($weekStart);
        }
        
        return $trends;
    }
}
```

---

### **Step 3: Update Controllers**

Update existing controllers to call productivity tracking:

**In ReferenceController when marking paper as read:**
```php
public function updateStatus(Request $request, Reference $reference)
{
    $reference->update(['status' => $request->status]);
    
    if ($request->status === 'completed') {
        // Log productivity session
        $weeklyGoal = WeeklyGoal::currentWeek()->first();
        if ($weeklyGoal) {
            $weeklyGoal->logActivity('papers_read');
        }
        
        // Update daily metrics
        app(ProductivityTracker::class)->aggregateDailyMetrics();
    }
    
    return redirect()->back()->with('success', 'Status updated');
}
```

**Similar updates for:**
- VocabularyController (when learning new terms)
- NoteController (when creating notes)
- TutorialController (when completing tutorials)

---

### **Step 4: Create Scheduled Tasks**

**File:** `app/Console/Kernel.php` or use Laravel 11 scheduler

```php
protected function schedule(Schedule $schedule): void
{
    // Generate weekly goals every Monday at 00:00
    $schedule->call(function () {
        app(WeeklyGoalGenerator::class)->generateForCurrentWeek();
    })->weeklyOn(1, '00:00');
    
    // Aggregate daily metrics at midnight
    $schedule->call(function () {
        app(ProductivityTracker::class)->aggregateDailyMetrics(now()->subDay());
    })->dailyAt('00:00');
}
```

---

### **Step 5: Run Migrations**

```bash
# Run the new migration
php artisan migrate

# If you have old tables, rollback first
php artisan migrate:rollback --step=1
php artisan migrate
```

---

### **Step 6: Seed Default Templates**

**File:** `database/seeders/RoadmapTemplateSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\RoadmapTemplate;
use Illuminate\Database\Seeder;

class RoadmapTemplateSeeder extends Seeder
{
    public function run(): void
    {
        RoadmapTemplate::create([
            'user_id' => 1,
            'name' => 'PhD Thesis Completion',
            'description' => 'Complete doctoral thesis from proposal to defense',
            'category' => 'academic',
            'phases' => [
                [
                    'name' => 'Literature Review',
                    'milestones' => [
                        [
                            'title' => 'Collect 50 relevant papers',
                            'linked_module' => 'references',
                            'success_metrics' => ['papers_collected' => 50],
                            'linked_goal_template_id' => 1, // Research Reading Sprint
                            'week_offset' => 0,
                        ],
                        [
                            'title' => 'Read and summarize 30 papers',
                            'linked_module' => 'references',
                            'success_metrics' => ['papers_read' => 30, 'summaries' => 30],
                            'linked_goal_template_id' => 1,
                            'week_offset' => 2,
                        ],
                    ],
                    'linked_modules' => ['references', 'notes'],
                ],
                // Add more phases...
            ],
            'estimated_duration_weeks' => 24,
        ]);
        
        // Add more templates...
    }
}
```

---

## 🎯 **How It All Works Together**

### **Example Flow: Literature Review Milestone**

1. **User activates "PhD Thesis" roadmap**
   - System creates roadmap from template
   - Auto-generates 12 milestones across 4 phases
   - Milestone 1.2: "Read and summarize 30 papers" linked to "Research Reading Sprint" template

2. **Week 1 begins (Monday 00:00)**
   - Scheduler triggers `WeeklyGoalGenerator`
   - Finds active milestone 1.2
   - Creates weekly goal from "Research Reading Sprint" template
   - Sets targets: 5 papers, 3 summaries, 10 vocab terms, 2 notes

3. **User works through the week**
   - Monday: Reads Paper #1 → Marks as "completed" in References module
   - Controller calls `weeklyGoal->logActivity('papers_read')`
   - Logs productivity session (30 min, 20 points)
   - Updates weekly goal achievements: `papers_read: 1/5`
   - Updates milestone progress: `1/30 papers`

4. **End of week**
   - Weekly goal shows: 87% completion
   - Milestone shows: 5/30 papers (17%)
   - Productivity metrics show: 923 points, 28 hours
   - Dashboard reflects all progress

5. **Repeat until milestone complete**
   - When milestone reaches 100%, auto-marks as completed
   - Roadmap progress recalculates
   - System moves to next phase automatically

---

## 📊 **Dashboard Data Structure**

```php
// In your DashboardController or ReportsController
public function index()
{
    return Inertia::render('dashboard', [
        'roadmaps' => [
            'active' => Roadmap::active()->with('milestones')->get(),
            'progress' => Roadmap::active()->get()->map->overall_progress,
        ],
        'weeklyGoals' => [
            'current' => WeeklyGoal::currentWeek()->get(),
            'completion_rate' => WeeklyGoal::currentWeek()->avg('completion_rate'),
        ],
        'productivity' => [
            'this_week' => ProductivityMetric::getWeeklySummary(now()->startOfWeek()),
            'trends' => app(ProductivityTracker::class)->getWeeklyTrends(4),
        ],
        'modules' => [
            'references' => Reference::count(),
            'vocabulary' => Vocabulary::count(),
            'notes' => Note::count(),
        ],
    ]);
}
```

---

## 🚀 **Next Steps**

1. ✅ **Review this guide**
2. ⏳ **Update remaining models** (Milestone, WeeklyGoal, ProductivitySession, ProductivityMetric)
3. ⏳ **Create service classes** (WeeklyGoalGenerator, ProductivityTracker)
4. ⏳ **Update controllers** to call productivity tracking
5. ⏳ **Set up scheduled tasks**
6. ⏳ **Run migrations**
7. ⏳ **Seed default templates**
8. ⏳ **Build React frontend components** (next phase)

---

## 💡 **Key Benefits of This Architecture**

✅ **Zero Manual Progress Tracking** - Activities auto-update everything  
✅ **Intelligent Automation** - Goals generate from milestones  
✅ **Evidence-Based Metrics** - Real data, not self-reported  
✅ **Unified Dashboard** - Everything connected  
✅ **Scalable** - Easy to add new modules  
✅ **Gamified** - Points, achievements, progress  
✅ **Template System** - Reusable blueprints  
✅ **Multi-Level Analytics** - Daily, weekly, long-term trends  

---

**Scholaris is now positioned as a true scholarly operating system!** 🎓✨
