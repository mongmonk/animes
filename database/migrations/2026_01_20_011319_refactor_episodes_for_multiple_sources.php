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
        Schema::table('episodes', function (Blueprint $table) {
            $table->dropColumn('video_embed_url');
        });

        Schema::create('episode_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('episode_id')->constrained()->onDelete('cascade');
            $table->string('source');
            $table->text('url');
            $table->timestamps();
        });

        Schema::create('episode_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('episode_id')->constrained()->onDelete('cascade');
            $table->string('format'); // mp4, mkv, x265
            $table->string('quality'); // 480p, 720p, 1080p
            $table->string('host'); // GD, AKIRA, etc
            $table->text('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episode_downloads');
        Schema::dropIfExists('episode_videos');

        Schema::table('episodes', function (Blueprint $table) {
            $table->string('video_embed_url')->nullable();
        });
    }
};
