<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('tim_teknisi')) {
            Schema::create('tim_teknisi', function (Blueprint $t) {
                $t->id();
                $t->string('team_name')->unique(); // ex: PGC01
                $t->string('sto_base', 16)->index(); // ex: PGC
                $t->string('nik_teknisi1')->nullable()->index();
                $t->string('nik_teknisi2')->nullable()->index();
                $t->string('mitra')->nullable();
                $t->string('status_tim')->nullable(); // aktif/nonaktif (opsional)
                $t->timestamps();
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('tim_teknisi');
    }
};
