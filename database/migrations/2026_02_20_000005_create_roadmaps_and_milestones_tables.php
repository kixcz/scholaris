<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roadmaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('planned'); // planned, in_progress, completed
            $table->date('start_date');
            $table->date('end_date');
            $table->string('color')->default('#3498db');
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });

        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roadmap_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('target_date');
            $table->string('status')->default('pending'); // pending, completed
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index('roadmap_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('roadmaps');
    }
};
