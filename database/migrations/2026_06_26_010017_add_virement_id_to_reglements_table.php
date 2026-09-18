<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reglements', function (Blueprint $table) {
            $table->foreignId('virement_id')->nullable()->after('statut')
                ->constrained('virements')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reglements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('virement_id');
        });
    }
};
