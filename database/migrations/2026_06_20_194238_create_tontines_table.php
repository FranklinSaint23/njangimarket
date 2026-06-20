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
        Schema::create('tontines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('createur_id')->constrained('users')->onDelete('cascade');
            $table->string('nom');
            $table->text('description')->nullable();
            $table->decimal('montant_cotisation', 12, 2);
            $table->integer('nb_membres_max')->default(50);
            $table->decimal('fond_total', 12, 2)->default(0);
            $table->enum('statut', ['ouvert', 'en_cours', 'clos'])->default('ouvert');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tontines');
    }
};
