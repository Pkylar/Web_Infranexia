<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('detail_order_psbs', function (Blueprint $table) {
            if (!Schema::hasColumn('detail_order_psbs', 'sub_kendala')) {
                $table->string('sub_kendala', 255)->nullable()->after('order_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('detail_order_psbs', function (Blueprint $table) {
            if (Schema::hasColumn('detail_order_psbs', 'sub_kendala')) {
                $table->dropColumn('sub_kendala');
            }
        });
    }
};
