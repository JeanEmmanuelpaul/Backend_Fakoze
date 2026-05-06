<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dons', function (Blueprint $table) {
            $table->id();
            $table->integer('montant');                        // montant en centimes ou HTG
            $table->enum('frequence', ['unique', 'mensuel'])->default('unique');
            $table->text('message')->nullable();
            $table->string('stripe_payment_intent_id')->unique()->nullable();
            $table->string('stripe_client_secret')->nullable();
            $table->enum('statut', ['pending', 'succeeded', 'failed'])->default('pending');
            $table->boolean('email_recu')->default(false);
            $table->timestamps();

            $table->index('statut');
            $table->index('stripe_payment_intent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dons');
    }
};
