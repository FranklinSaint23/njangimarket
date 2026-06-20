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
        Schema::create('livraisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commandes')->onDelete('cascade');
            $table->foreignId('livreur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('statut', ['assignee', 'en_route', 'livree', 'echec'])->default('assignee');
            $table->decimal('latitude_livreur', 10, 7)->nullable();
            $table->decimal('longitude_livreur', 10, 7)->nullable();
            $table->decimal('commission', 12, 2)->default(0);
            $table->decimal('bonus_satisfaction', 12, 2)->default(0);
            $table->timestamp('pris_en_charge_le')->nullable();
            $table->timestamp('livre_le')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livraisons');
    }
};
