<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('territory_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('sub_district', 50);
            $table->string('branch', 50)->nullable();
            $table->string('wok', 50)->nullable();
            $table->string('service_area', 80)->nullable();
            $table->string('sto', 10);
            $table->index(['sub_district','branch','wok','service_area','sto'], 'territory_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('territory_mappings');
    }
};
