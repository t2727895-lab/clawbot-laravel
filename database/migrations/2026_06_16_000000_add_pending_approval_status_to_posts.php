<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE posts MODIFY status ENUM('pending_approval', 'queued', 'rejected', 'published', 'failed') DEFAULT 'queued'");
        }
        // For SQLite - need to recreate the column
        else {
            Schema::table('posts', function (Blueprint $table) {
                // SQLite doesn't support modifying enum columns directly
                // This migration assumes the application handles it or uses a different approach
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For MySQL
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE posts MODIFY status ENUM('queued', 'rejected', 'published', 'failed') DEFAULT 'queued'");
        }
    }
};
