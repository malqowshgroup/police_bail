<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('libelle')->comment('Ex: Commissaire, Commandant, Capitaine, Lieutenant, Brigadier-chef, Brigadier, Gardien de la paix');
            $table->decimal('taux_bail', 10, 2)->comment('Montant mensuel du loyer pour ce grade');
            $table->integer('ordre')->default(0)->comment('Ordre de tri');
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
