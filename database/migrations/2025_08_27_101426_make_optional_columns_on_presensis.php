<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Paling aman/tanpa dbal: raw SQL
        DB::statement('ALTER TABLE presensis MODIFY nama VARCHAR(191) NULL');
        // Jika perlu kolom lain juga optional:
        // DB::statement('ALTER TABLE presensis MODIFY tim_id BIGINT UNSIGNED NULL');
        // DB::statement('ALTER TABLE presensis MODIFY checked_out_at TIMESTAMP NULL');
        // DB::statement('ALTER TABLE presensis MODIFY catatan TEXT NULL'); 
    }

    public function down(): void
    {
        // Balik lagi jadi NOT NULL kalau dibutuhkan
        DB::statement('ALTER TABLE presensis MODIFY nama VARCHAR(191) NOT NULL');
    }
};

