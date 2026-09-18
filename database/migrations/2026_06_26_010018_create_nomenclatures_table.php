<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nomenclatures', function (Blueprint $table) {
            $table->id();
            $table->string('categorie')->comment('Ex: statut_contrat, type_document…');
            $table->string('code')->comment('Valeur technique (ex: actif). Immuable pour les entrées système.');
            $table->string('libelle');
            $table->string('couleur_badge')->nullable()->comment('Classes Tailwind du badge');
            $table->string('couleur_dot')->nullable()->comment('Classe Tailwind de la pastille');
            $table->integer('ordre')->default(0);
            $table->boolean('actif')->default(true);
            $table->boolean('systeme')->default(false)->comment('Câblée à la logique métier : non supprimable, code non modifiable');
            $table->timestamps();

            $table->unique(['categorie', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomenclatures');
    }
};
