<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('rekap_photos', function (Blueprint $table) {
            if (Schema::hasColumn('rekap_photos','file_path')) {
                $table->dropColumn('file_path');
            }
        });
    }
    public function down(): void {
        Schema::table('rekap_photos', function (Blueprint $table) {
            if (!Schema::hasColumn('rekap_photos','file_path')) {
                $table->string('file_path'); // akan kosong saat rollback
            }
        });
    }
};
