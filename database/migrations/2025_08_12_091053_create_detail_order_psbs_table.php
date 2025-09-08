<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_order_psbs', function (Blueprint $table) {
            $table->id();

            // === Kolom-kolom bisnis ===
            $table->timestamp('date_created')->nullable()->index();     // auto diisi saat create/import
            $table->string('workorder', 50)->nullable()->index();
            $table->string('sc_order_no', 120)->nullable()->index();    // SC Order No/Track ID/CSRM No
            $table->string('service_no', 60)->nullable()->index();

            $table->text('description')->nullable();
            $table->string('status_bima', 60)->nullable();

            $table->text('address')->nullable();
            $table->string('customer_name', 120)->nullable()->index();
            $table->string('contact_number', 40)->nullable();

            $table->string('team_name', 80)->nullable()->index();
            $table->string('order_status', 120)->nullable()->index();   // OPEN/SURVEI/…/CLOSE

            $table->longText('work_log')->nullable();                   // auto append setiap perubahan status

            $table->string('koordinat_survei', 80)->nullable();

            // Validasi kendala
            $table->string('validasi_eviden_kendala', 20)->nullable();  // VALID / NON VALID / BELUM DI VALIDASI
            $table->string('nama_validator_kendala', 120)->nullable();

            // Validasi failwa/invalid survey
            $table->string('validasi_failwa_invalid', 30)->nullable();  // VALID / NON VALID / BELUM DI VALIDASI
            $table->string('nama_validator_failwa', 120)->nullable();

            $table->text('keterangan_non_valid')->nullable();
            $table->string('id_valins', 100)->nullable()->index();

            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_order_psbs');
    }
};
