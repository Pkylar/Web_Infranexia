<?php

// database/migrations/2025_08_25_000001_create_teknisis_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('teknisis', function (Blueprint $t) {
            $t->id();
            $t->string('nik')->unique();          // NIK teknisi
            $t->string('nama');
            $t->string('mitra')->nullable();
            $t->string('base_sto', 10)->nullable();
            $t->string('foto_path')->nullable();  // simpan path kalau ada
            $t->string('status')->default('AKTIF'); // AKTIF / NONAKTIF
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('teknisis'); }
};

