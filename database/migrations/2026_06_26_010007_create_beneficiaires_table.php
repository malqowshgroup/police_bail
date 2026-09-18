<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proprietaire_id')->constrained('proprietaires');
            $table->string('nom');
            $table->string('prenoms')->nullable();
            $table->enum('type_beneficiaire', ['proprietaire', 'procuration', 'decision_judiciaire']);
            $table->string('num_compte_bancaire');
            $table->string('banque');
            $table->string('code_banque')->nullable();
            $table->string('agence')->nullable();
            $table->decimal('montant_dette', 12, 2)->nullable()->comment('Pour les décisions judiciaires');
            $table->decimal('montant_rembourse', 12, 2)->default(0)->comment('Pour les décisions judiciaires');
            $table->date('date_fin_procuration')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiaires');
    }
};
