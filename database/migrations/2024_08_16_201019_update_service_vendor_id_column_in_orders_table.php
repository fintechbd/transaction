<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('service_vendor_id')
                ->default(config('fintech.business.default_vendor', 1))
                ->after('service_id');
            $table->string('vendor')
                ->default(Str::slug(config('fintech.business.default_vendor_name'), '_'))
                ->after('service_vendor_id');
            $table->json('timeline')
                ->nullable()
                ->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('service_vendor_id');
            $table->dropColumn('timeline');
            $table->dropColumn('vendor');
        });
    }
};
