// database/migrations/xxxx_create_evenements_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evenements', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('lieu')->nullable();
            $table->dateTime('date');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('statut')->default('planifié'); // planifié | en_cours | terminé | annulé
            $table->integer('capacite')->nullable();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evenements');
    }
};
