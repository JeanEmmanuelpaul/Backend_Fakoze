<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galeries', function (Blueprint $table) {
            $table->id();

            // ── Clé étrangère vers articles ───────────────────────────────────
            $table->foreignId('article_id')
                  ->constrained('articles')
                  ->onDelete('cascade');   // ← supprime la galerie si l'article est supprimé

            $table->string('titre');
            $table->text('description')->nullable();

            // ── 10 champs image nullable ──────────────────────────────────────
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->string('image4')->nullable();
            $table->string('image5')->nullable();
            $table->string('image6')->nullable();
            $table->string('image7')->nullable();
            $table->string('image8')->nullable();
            $table->string('image9')->nullable();
            $table->string('image10')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galeries');
    }
};
