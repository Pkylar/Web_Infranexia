<?php

// database/migrations/2025_08_25_000003_create_presensis_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('presensis', function (Blueprint $t) {
            $t->id();
            $t->string('nik');                 // teknisis.nik
            $t->foreignId('tim_id')->constrained('tim_teknisis')->cascadeOnUpdate();
            $t->string('sto_now', 10);         // STO tempat checkin
            $t->timestamp('checked_in_at')->useCurrent();
            $t->timestamp('checked_out_at')->nullable();
            $t->string('catatan')->nullable();
            $t->timestamps();

            // Satu teknisi jangan double checkin di tim yang sama di hari yang sama:
            $t->index(['nik','tim_id','checked_in_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('presensis'); }
};

