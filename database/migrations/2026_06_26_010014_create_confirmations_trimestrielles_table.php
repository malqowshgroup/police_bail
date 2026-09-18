<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('confirmations_trimestrielles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_bail_id')->constrained('contrats_bail');
            $table->tinyInteger('trimestre')->unsigned()->comment('1 à 4');
            $table->year('annee');
            $table->boolean('confirmation_policier')->nullable();
            $table->dateTime('date_confirmation_policier')->nullable();
            $table->boolean('confirmation_proprietaire')->nullable();
            $table->dateTime('date_confirmation_proprietaire')->nullable();
            $table->string('code_securite_policier', 6)->nullable();
            $table->string('code_securite_proprietaire', 6)->nullable();
            $table->enum('statut', [
                'en_attente',
                'confirme',
                'suspendu_policier',
                'suspendu_proprietaire',
            ])->default('en_attente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('confirmations_trimestrielles');
    }
};
