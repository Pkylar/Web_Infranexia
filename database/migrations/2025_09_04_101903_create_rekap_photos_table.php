<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rekap_photos', function (Blueprint $t) {
            $t->id();
            $t->string('file_path');         // path file di storage
            $t->string('title', 150)->nullable();
            $t->string('sto', 20)->nullable();
            $t->text('note')->nullable();    // catatan singkat (opsional)
            $t->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('rekap_photos'); }
};
