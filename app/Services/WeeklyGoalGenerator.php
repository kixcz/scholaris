<?php

namespace App\Services;

use App\Models\Roadmap;
use App\Models\Milestone;
use App\Models\WeeklyGoalTemplate;
use App\Models\WeeklyGoal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WeeklyGoalGenerator
{
    /**
     * Generate weekly goals for all active roadmaps for the current week
     * 
     * @return int Number of goals generated
     */
    public function generateForCurrentWeek(): int
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $count = 0;

        Log::info("Starting weekly goal generation for week: {$weekStart->format('Y-m-d')} to {$weekEnd->format('Y-m-d')}");

        // Get all active roadmaps
        $activeRoadmaps = Roadmap::where('status', 'active')
            ->with(['milestones' => function ($query) {
                $query->whereIn('status', ['pending', 'in_progress'])
                      ->whereNotNull('linked_goal_id')
                      ->orderBy('phase_number')
                      ->orderBy('sequence_order');
            }])
            ->get();

        foreach ($activeRoadmaps as $roadmap) {
            $generated = $this->generateForRoadmap($roadmap, $weekStart, $weekEnd);
            $count += count($generated);
        }

        Log::info("Weekly goal generation complete. Generated {$count} goals.");

        return $count;
    }

    /**
     * Generate weekly goals for a specific roadmap
     * 
     * @return array Generated weekly goals
     */
    public function generateForRoadmap(Roadmap $roadmap, Carbon $weekStart, Carbon $weekEnd): array
    {
        $generated = [];
        $currentMilestones = $roadmap->milestones;

        foreach ($currentMilestones as $milestone) {
            // Check if goal already exists for this milestone this week
            $existingGoal = WeeklyGoal::where('milestone_id', $milestone->id)
                ->where('week_start_date', $weekStart)
                ->where('week_end_date', $weekEnd)
                ->first();

            if ($existingGoal) {
                Log::debug("Goal already exists for milestone: {$milestone->title}");
                continue;
            }

            // Get the linked template
            $template = WeeklyGoalTemplate::find($milestone->linked_goal_id);
            
            if (!$template) {
                Log::warning("No template found for milestone: {$milestone->title}");
                continue;
            }

            // Create weekly goal from template
            $goal = $template->createWeeklyGoal([
                'milestone_id' => $milestone->id,
                'roadmap_id' => $roadmap->id,
                'week_start_date' => $weekStart,
                'week_end_date' => $weekEnd,
                'auto_generated' => true,
                'title' => "{$template->name} - {$roadmap->title} (Week of {$weekStart->format('M d')})",
            ]);

            $generated[] = $goal;

            Log::info("Generated goal: {$goal->title} for milestone: {$milestone->title}");

            // Update milestone status to in_progress
            if ($milestone->status === 'pending') {
                $milestone->update(['status' => 'in_progress']);
            }
        }

        return $generated;
    }

    /**
     * Generate goals for a specific milestone
     */
    public function generateForMilestone(Milestone $milestone, Carbon $weekStart, Carbon $weekEnd): ?WeeklyGoal
    {
        // Check if goal already exists
        $existingGoal = WeeklyGoal::where('milestone_id', $milestone->id)
            ->where('week_start_date', $weekStart)
            ->first();

        if ($existingGoal) {
            return $existingGoal;
        }

        // Get template
        $template = WeeklyGoalTemplate::find($milestone->linked_goal_id);
        
        if (!$template) {
            return null;
        }

        // Create goal
        return $template->createWeeklyGoal([
            'milestone_id' => $milestone->id,
            'roadmap_id' => $milestone->roadmap_id,
            'week_start_date' => $weekStart,
            'week_end_date' => $weekEnd,
            'auto_generated' => true,
        ]);
    }

    /**
     * Get preview of goals that would be generated (without creating them)
     */
    public function previewGeneration(Carbon $weekStart = null): array
    {
        $weekStart = $weekStart ?? Carbon::now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();
        $preview = [];

        $activeRoadmaps = Roadmap::where('status', 'active')
            ->with(['milestones' => function ($query) {
                $query->whereIn('status', ['pending', 'in_progress'])
                      ->whereNotNull('linked_goal_id');
            }])
            ->get();

        foreach ($activeRoadmaps as $roadmap) {
            foreach ($roadmap->milestones as $milestone) {
                $template = WeeklyGoalTemplate::find($milestone->linked_goal_id);
                
                if ($template) {
                    $preview[] = [
                        'roadmap' => $roadmap->title,
                        'milestone' => $milestone->title,
                        'template' => $template->name,
                        'targets' => $template->default_targets,
                        'estimated_hours' => $template->estimated_hours,
                        'week_start' => $weekStart->format('Y-m-d'),
                        'week_end' => $weekEnd->format('Y-m-d'),
                    ];
                }
            }
        }

        return $preview;
    }

    /**
     * Manually trigger goal generation for testing
     */
    public function manualGenerate(): array
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        
        // Clear existing goals for this week (for testing)
        WeeklyGoal::where('week_start_date', $weekStart)
            ->where('auto_generated', true)
            ->delete();

        $count = $this->generateForCurrentWeek();
        
        return [
            'message' => "Generated {$count} weekly goals",
            'count' => $count,
            'week_start' => $weekStart->format('Y-m-d'),
            'week_end' => $weekEnd->format('Y-m-d'),
        ];
    }
}
