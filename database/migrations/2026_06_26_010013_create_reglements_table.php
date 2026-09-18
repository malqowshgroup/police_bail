<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reglements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_bail_id')->constrained('contrats_bail');
            $table->tinyInteger('periode_mois')->unsigned()->comment('1 à 12');
            $table->year('periode_annee');
            $table->enum('type_reglement', ['loyer_mensuel', 'ariere', 'rappel_grade', 'trop_percu']);
            $table->decimal('montant', 12, 2);
            $table->enum('statut', ['a_payer', 'en_attente_virement', 'vire', 'annule'])->default('a_payer');
            $table->string('numero_virement')->nullable();
            $table->date('date_virement')->nullable();
            $table->string('banque_bailleur')->nullable();
            $table->string('reference_fichier_virement')->nullable();
            $table->foreignId('genere_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reglements');
    }
};
