<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
        });

        Schema::create('article_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('editor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->longText('body');
            $table->string('status');
            $table->text('excerpt')->nullable();
            $table->json('snapshot')->nullable();
            $table->timestamps();
        });

        foreach (['event_photos', 'tv_videos'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('excerpt')->nullable();
                $table->longText('body')->nullable();
                $table->string('status')->default('draft')->index();
                $table->timestamp('published_at')->nullable()->index();
                $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
                $table->unsignedInteger('sort_order')->default(0);
                $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tv_videos');
        Schema::dropIfExists('event_photos');
        Schema::dropIfExists('article_revisions');
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
        });
    }
};