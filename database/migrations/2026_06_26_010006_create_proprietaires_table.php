<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proprietaires', function (Blueprint $table) {
            $table->id();
            $table->enum('type_personne', ['physique', 'morale'])->default('physique');
            $table->string('nom');
            $table->string('prenoms')->nullable();
            $table->string('raison_sociale')->nullable()->comment('Pour les personnes morales');
            $table->string('num_piece_identite');
            $table->enum('type_piece', ['cni', 'passeport', 'sejour']);
            $table->string('adresse_postale')->nullable();
            $table->string('telephone');
            $table->string('telephone2')->nullable();
            $table->string('email')->nullable();
            $table->string('num_compte_contribuable')->nullable();
            $table->foreignId('localite_id')->nullable()->constrained('localites')->nullOnDelete();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proprietaires');
    }
};
