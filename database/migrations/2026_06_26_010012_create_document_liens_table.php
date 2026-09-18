<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_liens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->enum('entite_type', [
                'policier',
                'logement_civil',
                'proprietaire',
                'contrat_bail',
                'bordereau',
            ]);
            $table->unsignedBigInteger('entite_id');
            $table->timestamps();

            $table->index(['entite_type', 'entite_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_liens');
    }
};
