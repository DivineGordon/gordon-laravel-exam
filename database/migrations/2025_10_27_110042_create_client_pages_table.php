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
        Schema::create('client_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique(); // URL identifier for the page
            $table->json('content'); // Store editable text content
            $table->string('logo_path')->nullable();
            $table->string('background_image_path')->nullable();
            $table->foreignId('theme_id')->nullable()->constrained('page_themes');
            $table->boolean('is_published')->default(false);
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_pages');
    }
};
