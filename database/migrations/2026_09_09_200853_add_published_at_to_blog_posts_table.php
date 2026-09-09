<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('published');
        });

        // Backfill: for posts already published before this column existed,
        // created_at is the best available approximation of when they went
        // live — see BlogPost's saving() hook for how it's set going forward.
        DB::table('blog_posts')->where('published', true)->update([
            'published_at' => DB::raw('created_at'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn('published_at');
        });
    }
};
