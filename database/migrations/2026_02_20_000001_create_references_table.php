<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('authors');
            $table->integer('year');
            $table->string('journal')->nullable();
            $table->string('doi')->nullable();
            $table->string('url')->nullable();
            $table->string('type')->default('Paper'); // Paper, Book, Report, Article, Conference
            $table->integer('tier')->default(1); // 1-5
            $table->string('pillar')->default('XAI'); // XAI, GeoAI, Causal Inference, etc.
            $table->string('status')->default('unread'); // unread, reading, completed
            $table->string('pdf_path')->nullable();
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'tier']);
            $table->index(['user_id', 'pillar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('references');
    }
};
