<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rekap_photos', function (Blueprint $table) {
            if (!Schema::hasColumn('rekap_photos','photo_path')) {
                $table->string('photo_path')->after('id');
            }
            if (!Schema::hasColumn('rekap_photos','sto')) {
                $table->string('sto', 10)->nullable()->after('photo_path');
            }
            if (!Schema::hasColumn('rekap_photos','teknisi_nik')) {
                $table->string('teknisi_nik', 30)->nullable()->after('sto');
            }
            if (!Schema::hasColumn('rekap_photos','teknisi_nama')) {
                $table->string('teknisi_nama', 120)->after('teknisi_nik');
            }
            if (!Schema::hasColumn('rekap_photos','note')) {
                $table->string('note', 200)->nullable()->after('teknisi_nama');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rekap_photos', function (Blueprint $table) {
            if (Schema::hasColumn('rekap_photos','photo_path'))   $table->dropColumn('photo_path');
            if (Schema::hasColumn('rekap_photos','sto'))          $table->dropColumn('sto');
            if (Schema::hasColumn('rekap_photos','teknisi_nik'))  $table->dropColumn('teknisi_nik');
            if (Schema::hasColumn('rekap_photos','teknisi_nama')) $table->dropColumn('teknisi_nama');
            if (Schema::hasColumn('rekap_photos','note'))         $table->dropColumn('note');
        });
    }
};
