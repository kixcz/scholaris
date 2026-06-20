<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ScholarlySystemSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📚 Seeding Scholarly System Templates...');

        $systemUser = User::firstOrCreate(
            ['email' => 'system@scholaris.local'],
            ['name' => 'Scholaris System', 'password' => bcrypt('system')]
        );

        $userId = $systemUser->id;
        $now = now();

        $this->seedRoadmapTemplates($userId, $now);
        $this->seedWeeklyGoalTemplates($userId, $now);

        $this->command->info('✅ Scholarly system templates seeded successfully!');
    }

    private function seedRoadmapTemplates(int $userId, $now): void
    {
        $this->command->info('🗺️ Creating roadmap templates...');

        $templates = [
            [
                'name' => 'PhD Thesis Completion',
                'description' => 'Complete your PhD thesis from proposal to defense',
                'category' => 'thesis',
                'estimated_duration_weeks' => 24,
                'phases' => json_encode([
                    ['name' => 'Literature Review', 'duration_weeks' => 6],
                    ['name' => 'Research & Data Collection', 'duration_weeks' => 8],
                    ['name' => 'Writing & Analysis', 'duration_weeks' => 6],
                    ['name' => 'Review & Defense', 'duration_weeks' => 4],
                ]),
                'success_criteria' => json_encode(['thesis_submitted' => true, 'papers_published' => 2]),
                'linked_modules' => json_encode(['references', 'notes', 'vocabulary', 'tasks']),
            ],
            [
                'name' => 'Research Paper Publication',
                'description' => 'Complete a research paper and submit to journal',
                'category' => 'research',
                'estimated_duration_weeks' => 16,
                'phases' => json_encode([
                    ['name' => 'Literature Review', 'duration_weeks' => 4],
                    ['name' => 'Research & Writing', 'duration_weeks' => 8],
                    ['name' => 'Review & Submission', 'duration_weeks' => 4],
                ]),
                'success_criteria' => json_encode(['paper_submitted' => true, 'references_reviewed' => 50]),
                'linked_modules' => json_encode(['references', 'notes', 'tasks']),
            ],
            [
                'name' => 'Machine Learning Mastery',
                'description' => 'Master machine learning fundamentals and advanced topics',
                'category' => 'skill',
                'estimated_duration_weeks' => 12,
                'phases' => json_encode([
                    ['name' => 'Fundamentals', 'duration_weeks' => 4],
                    ['name' => 'Algorithms & Implementation', 'duration_weeks' => 6],
                ]),
                'success_criteria' => json_encode(['tutorials_completed' => 20, 'projects_built' => 5]),
                'linked_modules' => json_encode(['tutorials', 'vocabulary', 'tasks', 'links']),
            ],
        ];

        foreach ($templates as $template) {
            DB::table('roadmap_templates')->insert(array_merge($template, [
                'user_id' => $userId,
                'is_public' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        $this->command->info('  ✅ 3 roadmap templates created');
    }

    private function seedWeeklyGoalTemplates(int $userId, $now): void
    {
        $this->command->info('🎯 Creating weekly goal templates...');

        $templates = [
            [
                'name' => 'Research Reading Sprint',
                'description' => 'Intensive literature review and paper reading week',
                'category' => 'research',
                'default_targets' => json_encode(['papers_to_read' => 5, 'summaries_to_write' => 3, 'vocabulary_terms' => 10]),
                'estimated_hours' => 15,
            ],
            [
                'name' => 'Academic Writing Sprint',
                'description' => 'Focused writing week for papers, thesis, or reports',
                'category' => 'writing',
                'default_targets' => json_encode(['words_to_write' => 2000, 'citations_to_add' => 10, 'sections_to_draft' => 1]),
                'estimated_hours' => 20,
            ],
            [
                'name' => 'Thesis Development Sprint',
                'description' => 'Work on thesis chapters and research',
                'category' => 'thesis',
                'default_targets' => json_encode(['chapters_to_write' => 1, 'papers_to_review' => 8, 'data_analysis_tasks' => 3]),
                'estimated_hours' => 25,
            ],
            [
                'name' => 'Skill Development Sprint',
                'description' => 'Learn new technical skills through tutorials and practice',
                'category' => 'skill',
                'default_targets' => json_encode(['tutorials_to_complete' => 3, 'exercises_to_practice' => 20, 'vocabulary_terms' => 15]),
                'estimated_hours' => 18,
            ],
            [
                'name' => 'Project Development Sprint',
                'description' => 'Build and develop a project with concrete milestones',
                'category' => 'project',
                'default_targets' => json_encode(['features_to_implement' => 3, 'bugs_to_fix' => 5, 'tests_to_write' => 10]),
                'estimated_hours' => 22,
            ],
            [
                'name' => 'Literature Review Sprint',
                'description' => 'Comprehensive literature review and organization',
                'category' => 'research',
                'default_targets' => json_encode(['papers_to_collect' => 15, 'papers_to_read' => 10, 'summaries_to_write' => 5]),
                'estimated_hours' => 20,
            ],
            [
                'name' => 'Data Analysis Sprint',
                'description' => 'Focus on data collection, cleaning, and analysis',
                'category' => 'research',
                'default_targets' => json_encode(['datasets_to_analyze' => 2, 'visualizations_to_create' => 5, 'results_to_document' => 3]),
                'estimated_hours' => 16,
            ],
        ];

        foreach ($templates as $template) {
            DB::table('weekly_goal_templates')->insert(array_merge($template, [
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        $this->command->info('  ✅ 7 weekly goal templates created');
    }
}
