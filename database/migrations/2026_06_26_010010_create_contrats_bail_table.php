<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats_bail', function (Blueprint $table) {
            $table->id();
            $table->string('numero_contrat')->unique()->comment('Numéro généré');
            $table->foreignId('bordereau_id')->nullable()->constrained('bordereaux')->nullOnDelete();
            $table->foreignId('policier_id')->constrained('policiers');
            $table->foreignId('logement_civil_id')->constrained('logements_civils');
            $table->foreignId('grade_id')->constrained('grades')->comment('Grade au moment de la création du contrat');
            $table->decimal('taux_bail', 10, 2)->comment('Montant au moment de la création du contrat');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->enum('statut', ['en_attente', 'actif', 'suspendu', 'en_resiliation', 'resilie'])->default('en_attente');
            $table->text('motif_suspension')->nullable();
            $table->date('date_suspension')->nullable();
            $table->date('date_levee_suspension')->nullable();
            $table->date('date_resiliation')->nullable();
            $table->text('motif_resiliation')->nullable();
            $table->integer('preavis_mois')->default(3);
            $table->date('date_fin_preavis')->nullable();
            $table->boolean('avec_arrieres')->default(false);
            $table->integer('nb_mois_arrieres')->default(0);
            $table->date('date_debut_arrieres')->nullable();
            $table->foreignId('saisi_par')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('controle_par')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('date_validation')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats_bail');
    }
};
