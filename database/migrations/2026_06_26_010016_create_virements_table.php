<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virements', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique()->comment('Numéro généré : VIR-{année}-{séquence}');
            $table->foreignId('proprietaire_id')->constrained('proprietaires')->comment('Bailleur bénéficiaire');
            $table->string('banque')->nullable()->comment('Banque du bailleur');
            $table->decimal('montant_total', 14, 2)->default(0);
            $table->enum('statut', ['en_preparation', 'emis', 'execute', 'annule'])->default('en_preparation');
            $table->date('date_virement')->nullable()->comment('Date prévue / d\'exécution');
            $table->string('reference_fichier')->nullable()->comment('Référence du fichier de virement bancaire');
            $table->foreignId('emis_par')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('execute_par')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('date_emission')->nullable();
            $table->dateTime('date_execution')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virements');
    }
};
