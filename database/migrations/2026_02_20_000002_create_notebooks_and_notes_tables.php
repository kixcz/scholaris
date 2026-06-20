<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notebooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('color')->default('#2ecc71');
            $table->timestamps();
            
            $table->index('user_id');
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('notebook_id')->constrained()->onDelete('cascade');
            $table->foreignId('ref_id')->nullable()->constrained('references')->onDelete('set null');
            $table->text('text');
            $table->timestamps();
            
            $table->index(['user_id', 'notebook_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
        Schema::dropIfExists('notebooks');
    }
};
