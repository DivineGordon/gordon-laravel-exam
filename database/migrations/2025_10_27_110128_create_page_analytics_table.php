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
        Schema::create('page_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_page_id')->constrained()->onDelete('cascade');
            $table->string('visitor_ip');
            $table->string('session_id'); // To track unique visitors
            $table->string('user_agent');
            $table->string('referer')->nullable();
            $table->timestamp('visited_at');
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['client_page_id', 'visited_at']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_analytics');
    }
};
