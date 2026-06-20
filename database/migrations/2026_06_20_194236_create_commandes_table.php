<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('tontine_id')->nullable();
            $table->string('reference')->unique();
            $table->enum('type_livraison', ['livraison', 'pickup'])->default('livraison');
            $table->enum('statut', ['en_attente', 'confirmee', 'en_preparation', 'en_livraison', 'livree', 'annulee'])->default('en_attente');
            $table->enum('methode_paiement', ['orange_money', 'mtn_money', 'tontine'])->default('orange_money');
            $table->enum('statut_paiement', ['en_attente', 'paye', 'echoue', 'rembourse'])->default('en_attente');
            $table->decimal('sous_total', 12, 2);
            $table->decimal('frais_livraison', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->text('adresse_livraison')->nullable();
            $table->decimal('latitude_livraison', 10, 7)->nullable();
            $table->decimal('longitude_livraison', 10, 7)->nullable();
            $table->datetime('heure_souhaitee')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
