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
         Schema::create('page_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Dark Mode", "Ocean Blue"
            $table->string('primary_color'); // hex color
            $table->string('secondary_color');
            $table->string('accent_color');
            $table->string('text_color');
            $table->string('background_color');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_themes');
    }
};
