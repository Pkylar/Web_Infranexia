<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_order_psbs', function (Blueprint $table) {
            // Urutan kolomnya bisa kamu atur. Contoh diletakkan setelah address, lalu berurutan.
            $table->string('sub_district')->nullable()->after('address');
            $table->string('service_area')->nullable()->after('sub_district');
            $table->string('branch')->nullable()->after('service_area');
            $table->string('wok')->nullable()->after('branch');
            $table->string('sto')->nullable()->after('wok');
            $table->string('produk')->nullable()->after('sto');
            $table->string('transaksi')->nullable()->after('produk');
        });
    }

    public function down(): void
    {
        Schema::table('detail_order_psbs', function (Blueprint $table) {
            // MySQL aman drop sekaligus begini
            $table->dropColumn([
                'sub_district',
                'service_area',
                'branch',
                'wok',
                'sto',
                'produk',
                'transaksi',
            ]);
        });
    }
};
