<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocabulary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('term');
            $table->text('definition');
            $table->text('personal_understanding')->nullable();
            $table->text('example')->nullable();
            $table->json('related_refs')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('term');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary');
    }
};
