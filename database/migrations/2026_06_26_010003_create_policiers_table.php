<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policiers', function (Blueprint $table) {
            $table->id();
            $table->string('matricule')->unique();
            $table->string('nom');
            $table->string('prenoms');
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->enum('sexe', ['M', 'F']);
            $table->foreignId('grade_id')->constrained('grades');
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('localite_id')->nullable()->constrained('localites')->nullOnDelete();
            $table->enum('statut', [
                'actif',
                'suspendu',
                'retraite',
                'decede',
                'radie',
                'disponibilite',
                'demission',
                'hors_cadre',
                'detachement',
                'stagiaire',
            ])->default('actif');
            $table->date('date_prise_service')->nullable();
            $table->date('date_statut')->nullable()->comment('Date du changement de statut');
            $table->boolean('proprietaire_logement')->default(false)->comment('Le policier est propriétaire de son logement');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policiers');
    }
};
