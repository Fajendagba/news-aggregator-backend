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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('content');
            $table->string('author')->nullable();
            $table->string('url')->unique();
            $table->string('image_url')->nullable();
            $table->timestamp('published_at');
            $table->foreignId('source_id')->constrained()->onDelete('cascade');
            $table->string('category')->nullable();
            $table->timestamps();

            // Added indexes for better query performance
            $table->index(['published_at', 'source_id']);
            $table->index('category');
            $table->fulltext(['title', 'description', 'content']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
