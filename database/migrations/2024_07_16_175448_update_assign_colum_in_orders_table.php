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
        if (! Schema::hasColumn('orders', 'assigned_user_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('assigned_user_id')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'assigned_user_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('assigned_user_id');
            });
        }
    }
};
