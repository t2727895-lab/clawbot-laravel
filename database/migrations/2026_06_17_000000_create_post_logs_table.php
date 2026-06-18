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
        Schema::create('post_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('platform_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // submitted, approved, rejected, publish_attempt, published, failed
            $table->string('status'); // success, error
            $table->text('message')->nullable(); // error message or success note
            $table->json('response_data')->nullable(); // raw response from Clawbot/platform
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_logs');
    }
};
