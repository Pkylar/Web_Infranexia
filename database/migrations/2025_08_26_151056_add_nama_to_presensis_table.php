<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            // Tambah team_name kalau belum ada
            if (!Schema::hasColumn('presensis', 'team_name')) {
                $table->string('team_name', 50)->nullable();
            }
            // Tambah nama kalau belum ada
            if (!Schema::hasColumn('presensis', 'nama')) {
                $table->string('nama', 100)->nullable();
            }
            // Tambah checkin_at kalau belum ada
            if (!Schema::hasColumn('presensis', 'checkin_at')) {
                $table->timestamp('checkin_at')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            if (Schema::hasColumn('presensis', 'checkin_at')) $table->dropColumn('checkin_at');
            if (Schema::hasColumn('presensis', 'nama'))       $table->dropColumn('nama');
            // Hanya drop team_name kalau kamu yakin kolom ini dibuat di migration ini
            // if (Schema::hasColumn('presensis', 'team_name'))  $table->dropColumn('team_name');
        });
    }
};
