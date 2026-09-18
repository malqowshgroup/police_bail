<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('nom_original');
            $table->enum('type_document', [
                'carte_pro',
                'fiche_identite',
                'certificat_prise_service',
                'decision_grade',
                'piece_identite_proprio',
                'rib',
                'procuration',
                'declaration_fonciere',
                'contrat_bail_signe',
                'extrait_topographique',
                'etat_lieux_entree',
                'etat_lieux_sortie',
                'pv_remise_cles',
                'pv_restitution_cles',
                'attestation_occupation',
                'quitus_sodeci',
                'quitus_cie',
                'avis_resiliation',
                'decision_judiciaire',
                'autre',
            ]);
            $table->string('minio_bucket')->nullable();
            $table->string('minio_key')->nullable();
            $table->string('chemin_local')->nullable()->comment('Stockage local en fallback');
            $table->integer('taille_ko')->nullable();
            $table->string('format')->nullable()->comment('PDF, JPG, PNG, TIFF');
            $table->enum('statut', ['en_attente', 'valide', 'rejete'])->default('en_attente');
            $table->foreignId('uploaded_par')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('date_validation')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
