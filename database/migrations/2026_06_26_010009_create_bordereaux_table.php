<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bordereaux', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique()->comment('Numéro généré');
            $table->year('annee');
            $table->enum('statut', ['en_saisie', 'en_controle', 'en_validation', 'valide', 'rejete'])->default('en_saisie');
            $table->foreignId('saisi_par')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('controle_par')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('date_saisie')->nullable();
            $table->dateTime('date_controle')->nullable();
            $table->dateTime('date_validation')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bordereaux');
    }
};
