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
        Schema::create('tabel_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('tabel_santri')->cascadeOnDelete();
            $table->foreignId('kriteria_id')->constrained('tabel_kriteria')->cascadeOnDelete();
            $table->integer('nilai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabel_nilai');
    }
};
