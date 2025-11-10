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
        Schema::table('presensis', function (Blueprint $table) {
            // Hapus foreign key constraint-nya
            $table->dropForeign(['tim_id']);

            // hapus kolomnya
            $table->dropColumn('tim_id');
        });
    }

    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            // Tambahkan kembali kolom dan constraint kalau rollback
            $table->foreignId('tim_id')
                ->constrained('tim_teknisi')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

};
