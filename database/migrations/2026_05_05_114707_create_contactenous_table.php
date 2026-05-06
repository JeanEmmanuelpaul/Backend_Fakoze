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
        Schema::create('contactenou', function (Blueprint $table) {
            $table->id();
            $table->string('prenom', 100);
            $table->string('nom', 100);
            $table->string('email', 255);
            $table->string('telephone', 30)->nullable();
            $table->enum('sujet', [
                'info',
                'partenariat',
                'don',
                'benevolat',
                'presse',
                'autre',
            ]);
            $table->text('message');
            $table->boolean('consentement')->default(false);
            $table->enum('statut', ['nouveau', 'lu', 'traite', 'archive'])->default('nouveau');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactenous');
    }
};
