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
        Schema::create('tabel_hasil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('tabel_santri')->cascadeOnDelete();
            $table->string('kluster'); // Cukup, Baik, Sangat Baik
            $table->double('jarak_c1'); // Jarak ke Centroid 1 (Cukup)
            $table->double('jarak_c2'); // Jarak ke Centroid 2 (Baik)
            $table->double('jarak_c3'); // Jarak ke Centroid 3 (Sangat Baik)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_hasil');
    }
};
