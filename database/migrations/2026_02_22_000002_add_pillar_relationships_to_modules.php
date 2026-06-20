<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('references', function (Blueprint $table) {
            $table->foreignId('pillar_id')->nullable()->after('user_id')->constrained('pillars')->onDelete('set null');
            $table->foreignId('domain_id')->nullable()->after('pillar_id')->constrained('domains')->onDelete('set null');
            $table->foreignId('topic_id')->nullable()->after('domain_id')->constrained('topics')->onDelete('set null');
        });

        Schema::table('notebooks', function (Blueprint $table) {
            $table->foreignId('pillar_id')->nullable()->after('user_id')->constrained('pillars')->onDelete('set null');
            $table->foreignId('domain_id')->nullable()->after('pillar_id')->constrained('domains')->onDelete('set null');
        });

        Schema::table('vocabulary', function (Blueprint $table) {
            $table->foreignId('pillar_id')->nullable()->after('user_id')->constrained('pillars')->onDelete('set null');
            $table->foreignId('domain_id')->nullable()->after('pillar_id')->constrained('domains')->onDelete('set null');
            $table->foreignId('topic_id')->nullable()->after('domain_id')->constrained('topics')->onDelete('set null');
        });

        Schema::table('roadmaps', function (Blueprint $table) {
            $table->foreignId('pillar_id')->nullable()->after('user_id')->constrained('pillars')->onDelete('set null');
            $table->foreignId('domain_id')->nullable()->after('pillar_id')->constrained('domains')->onDelete('set null');
        });

        Schema::table('weekly_goals', function (Blueprint $table) {
            $table->foreignId('pillar_id')->nullable()->after('user_id')->constrained('pillars')->onDelete('set null');
            $table->foreignId('domain_id')->nullable()->after('pillar_id')->constrained('domains')->onDelete('set null');
        });

        Schema::table('links', function (Blueprint $table) {
            $table->foreignId('pillar_id')->nullable()->after('user_id')->constrained('pillars')->onDelete('set null');
            $table->foreignId('domain_id')->nullable()->after('pillar_id')->constrained('domains')->onDelete('set null');
            $table->foreignId('topic_id')->nullable()->after('domain_id')->constrained('topics')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('references', function (Blueprint $table) {
            $table->dropForeign(['pillar_id']);
            $table->dropForeign(['domain_id']);
            $table->dropForeign(['topic_id']);
            $table->dropColumn(['pillar_id', 'domain_id', 'topic_id']);
        });

        Schema::table('notebooks', function (Blueprint $table) {
            $table->dropForeign(['pillar_id']);
            $table->dropForeign(['domain_id']);
            $table->dropColumn(['pillar_id', 'domain_id']);
        });

        Schema::table('vocabulary', function (Blueprint $table) {
            $table->dropForeign(['pillar_id']);
            $table->dropForeign(['domain_id']);
            $table->dropForeign(['topic_id']);
            $table->dropColumn(['pillar_id', 'domain_id', 'topic_id']);
        });

        Schema::table('roadmaps', function (Blueprint $table) {
            $table->dropForeign(['pillar_id']);
            $table->dropForeign(['domain_id']);
            $table->dropColumn(['pillar_id', 'domain_id']);
        });

        Schema::table('weekly_goals', function (Blueprint $table) {
            $table->dropForeign(['pillar_id']);
            $table->dropForeign(['domain_id']);
            $table->dropColumn(['pillar_id', 'domain_id']);
        });

        Schema::table('links', function (Blueprint $table) {
            $table->dropForeign(['pillar_id']);
            $table->dropForeign(['domain_id']);
            $table->dropForeign(['topic_id']);
            $table->dropColumn(['pillar_id', 'domain_id', 'topic_id']);
        });
    }
};
