<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old tables to recreate with new structure
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('roadmaps');

        // ===== ROADMAP TEMPLATES (Reusable Blueprints) =====
        Schema::create('roadmap_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Thesis Completion", "Research Paper Publication"
            $table->text('description')->nullable();
            $table->string('category')->default('academic'); // academic, research, career, certification, skill
            $table->json('phases')->nullable(); // Phase structure with expected outcomes
            $table->json('success_criteria')->nullable(); // How to measure completion
            $table->integer('estimated_duration_weeks')->nullable();
            $table->json('linked_modules')->nullable(); // Which modules this template uses
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'category']);
        });

        // ===== ACTIVE ROADMAPS (Instances of templates or custom) =====
        Schema::create('roadmaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('roadmap_templates')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('planned'); // planned, active, paused, completed, archived
            $table->date('start_date');
            $table->date('target_end_date');
            $table->date('actual_end_date')->nullable();
            $table->string('color')->default('#3498db');
            $table->integer('current_phase')->default(1);
            $table->decimal('overall_progress', 5, 2)->default(0); // 0.00 - 100.00
            $table->json('linked_modules')->nullable(); // Which modules contribute: [references, vocabulary, tutorials, notes]
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'current_phase']);
        });

        // ===== MILESTONES (Checkpoints within roadmaps) =====
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roadmap_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('phase_number'); // Which phase this belongs to
            $table->integer('sequence_order'); // Order within phase
            $table->date('target_date');
            $table->date('completed_at')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, completed, skipped
            $table->decimal('progress', 5, 2)->default(0); // 0.00 - 100.00
            $table->json('expected_outcomes')->nullable(); // What should be achieved
            $table->json('success_metrics')->nullable(); // How to measure completion
            $table->string('linked_module')->nullable(); // references, vocabulary, tutorials, notes, tasks
            $table->foreignId('linked_goal_id')->nullable(); // Links to weekly goal template
            $table->timestamps();
            
            $table->index(['roadmap_id', 'status']);
            $table->index(['roadmap_id', 'phase_number']);
            $table->index('linked_goal_id');
        });

        // ===== WEEKLY GOAL TEMPLATES (Reusable weekly patterns) =====
        Schema::create('weekly_goal_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Research Reading Sprint", "Writing Sprint"
            $table->text('description')->nullable();
            $table->string('category')->default('research'); // research, writing, skill, thesis, project
            $table->json('default_targets')->nullable(); // {literature_reviews: 5, tutorials: 2, vocabulary: 10, notes: 3}
            $table->integer('estimated_hours')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['user_id', 'category']);
            $table->index(['user_id', 'is_active']);
        });

        // ===== WEEKLY GOALS (Enhance existing table) =====
        Schema::dropIfExists('weekly_goals');

        Schema::create('weekly_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('weekly_goal_templates')->onDelete('set null');
            $table->foreignId('milestone_id')->nullable()->constrained('milestones')->onDelete('set null');
            $table->foreignId('roadmap_id')->nullable()->constrained('roadmaps')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->default('general');
            $table->string('urgency')->default('important'); // critical, important, routine
            $table->string('status')->default('planned'); // planned, active, in_progress, completed, failed
            $table->date('week_start_date');
            $table->date('week_end_date');
            $table->decimal('completion_rate', 5, 2)->default(0); // 0.00 - 100.00
            $table->json('targets')->nullable(); // {tasks: 10, reading: 5, tutorials: 2, vocabulary: 15}
            $table->json('achievements')->nullable(); // {tasks_completed: 8, reading_done: 5, etc.}
            $table->integer('actual_hours')->default(0);
            $table->boolean('auto_generated')->default(false); // Generated from milestone?
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'week_start_date']);
            $table->index(['roadmap_id', 'status']);
            $table->index(['milestone_id', 'status']);
        });

        // ===== PRODUCTIVITY TRACKER =====
        Schema::create('productivity_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('weekly_goal_id')->nullable()->constrained('weekly_goals')->onDelete('set null');
            $table->foreignId('task_id')->nullable(); // Will link to tasks module
            $table->string('activity_type'); // task, reading, tutorial, vocabulary, note, deep_work
            $table->string('module_source'); // references, vocabulary, tutorials, notes, tasks
            $table->integer('duration_minutes')->default(0);
            $table->integer('points_earned')->default(0); // Gamification
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional context
            $table->timestamp('session_date');
            $table->timestamps();
            
            $table->index(['user_id', 'session_date']);
            $table->index(['user_id', 'activity_type']);
            $table->index(['user_id', 'weekly_goal_id']);
        });

        // ===== PRODUCTIVITY METRICS (Aggregated daily/weekly) =====
        Schema::create('productivity_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('weekly_goal_id')->nullable()->constrained('weekly_goals')->onDelete('set null');
            $table->date('metric_date');
            $table->integer('tasks_completed')->default(0);
            $table->integer('references_reviewed')->default(0);
            $table->integer('tutorials_completed')->default(0);
            $table->integer('vocabulary_learned')->default(0);
            $table->integer('notes_created')->default(0);
            $table->integer('deep_work_minutes')->default(0);
            $table->integer('total_study_minutes')->default(0);
            $table->integer('total_points')->default(0);
            $table->decimal('productivity_score', 5, 2)->default(0); // 0-100
            $table->json('breakdown')->nullable(); // Detailed breakdown
            $table->timestamps();
            
            $table->unique(['user_id', 'metric_date']);
            $table->index(['user_id', 'metric_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productivity_metrics');
        Schema::dropIfExists('productivity_sessions');
        Schema::dropIfExists('weekly_goals');
        Schema::dropIfExists('weekly_goal_templates');
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('roadmaps');
        Schema::dropIfExists('roadmap_templates');
    }
};
