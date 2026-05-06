// database/migrations/xxxx_create_articles_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('image')->nullable();
            $table->string('lieu')->nullable();
            $table->text('description1')->nullable();
            $table->text('description2')->nullable();
            $table->text('description3')->nullable();
            $table->text('sou_description1')->nullable();
            $table->text('sou_description2')->nullable();
            $table->text('resume')->nullable();
            $table->text('resumearticle')->nullable();
            $table->string('categorie')->nullable();
            $table->string('auteur')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
