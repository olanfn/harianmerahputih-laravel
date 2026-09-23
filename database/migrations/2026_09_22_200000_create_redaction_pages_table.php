<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redaction_pages', function (Blueprint $table): void {
            $table->id();
            $table->longText('content');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        DB::table('redaction_pages')->insert([
            'id' => 1,
            'content' => '<h2>Redaksi</h2><p><strong>Harian Merah Putih</strong></p><p>Informasi susunan redaksi dapat diperbarui melalui panel admin.</p>',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('redaction_pages');
    }
};
