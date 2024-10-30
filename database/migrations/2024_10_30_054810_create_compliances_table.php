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
        Schema::create('compliances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('order_id')->nullable();
            $table->string('code');
            $table->string('name');
            $table->smallInteger('score')->default(0);
            $table->string('risk')->default('green');
            $table->string('priority')->default('green');
            $table->mediumText('remarks')->default('N/A');
            $table->mediumText('notes')->nullable();
            $table->dateTime('timestamp')->useCurrent();
            $table->json('compliances_data')->nullable();
            $table->foreignId('creator_id')->nullable();
            $table->foreignId('editor_id')->nullable();
            $table->foreignId('destroyer_id')->nullable();
            $table->foreignId('restorer_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('restored_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliances');
    }
};
