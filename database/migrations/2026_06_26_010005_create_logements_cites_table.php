<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logements_cites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cite_id')->constrained('cites_policieres');
            $table->string('reference');
            $table->string('type_logement')->nullable();
            $table->foreignId('policier_id')->nullable()->constrained('policiers')->nullOnDelete()->comment('Occupant actuel');
            $table->date('date_attribution')->nullable();
            $table->date('date_liberation')->nullable();
            $table->enum('statut', ['libre', 'occupe'])->default('libre');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logements_cites');
    }
};
