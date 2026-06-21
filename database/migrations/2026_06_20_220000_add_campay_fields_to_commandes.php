<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->string('campay_reference')->nullable()->after('statut_paiement');
            $table->text('note')->nullable()->after('adresse_livraison');
        });

        // Étendre l'enum statut (MySQL uniquement — SQLite stocke les enums comme string)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE commandes MODIFY COLUMN statut ENUM(
                'en_attente','confirmee','en_preparation','en_livraison','livree','annulee',
                'payee','paiement_en_cours'
            ) DEFAULT 'en_attente'");
        }
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn(['campay_reference', 'note']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE commandes MODIFY COLUMN statut ENUM(
                'en_attente','confirmee','en_preparation','en_livraison','livree','annulee'
            ) DEFAULT 'en_attente'");
        }
    }
};
