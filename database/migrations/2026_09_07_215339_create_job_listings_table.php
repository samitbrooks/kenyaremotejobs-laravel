<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Named job_listings, not jobs — Laravel's own queue system owns the
        // `jobs` table name. Primary key is the source-prefixed string id
        // used throughout (e.g. "arbeitnow--some-slug"), not an autoincrement,
        // since sync needs to upsert by that stable id across runs.
        Schema::create('job_listings', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('source_name');
            $table->string('source_id');
            $table->string('source_url', 2048);
            $table->string('title');
            $table->string('company');
            $table->longText('description');
            $table->json('tags');
            $table->string('location');
            $table->string('remote_type');
            $table->string('salary')->nullable();
            $table->json('annual_salary_usd')->nullable();
            $table->timestampTz('posted_at');
            $table->boolean('kenya_friendly');
            $table->unsignedTinyInteger('kenya_score');
            $table->json('kenya_reasons');
            // "synced" rows are owned by the sync job and get wiped/rebuilt
            // each run — origin distinguishes those from admin-authored or
            // employer-paid listings so a resync never deletes one of those.
            $table->string('origin')->default('synced');
            $table->string('tier')->default('basic');
            // No DB-level default — MariaDB rejects any DEFAULT clause on a
            // JSON/BLOB/TEXT column outright (MySQL 8 and SQLite both allow
            // it, which is what let this slip through local testing).
            // Every write path (sync, manual, employer, admin) always sets
            // this explicitly, so there's nothing that actually depends on
            // a database-side default existing.
            $table->json('audience_segments');
            $table->foreignId('posted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('posted_at');
            $table->index('source_name');
            $table->index('origin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
