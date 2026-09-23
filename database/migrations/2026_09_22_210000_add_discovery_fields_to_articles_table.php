<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->unsignedBigInteger('view_count')->default(0)->index()->after('published_at');
            $table->boolean('is_editor_pick')->default(false)->index()->after('view_count');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->dropColumn(['view_count', 'is_editor_pick']);
        });
    }
};
