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
        if (! Schema::hasColumn('blog_posts', 'image_url')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->string('image_url', 500)->nullable()->after('author_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('blog_posts', 'image_url')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->dropColumn('image_url');
            });
        }
    }
};
