<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logements_civils', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique()->comment('Généré: localité+année+séquence');
            $table->foreignId('localite_id')->constrained('localites');
            $table->string('quartier');
            $table->string('ilot')->nullable();
            $table->string('lot')->nullable();
            $table->text('adresse_complete')->nullable();
            $table->foreignId('proprietaire_id')->constrained('proprietaires');
            $table->foreignId('beneficiaire_id')->nullable()->constrained('beneficiaires')->nullOnDelete()->comment('Bénéficiaire actif actuel');
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logements_civils');
    }
};
