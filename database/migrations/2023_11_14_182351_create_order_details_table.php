<?php

use Fintech\Core\Enums\Auth\RiskProfile;
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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable();
            $table->foreignId('source_country_id')->nullable();
            $table->foreignId('destination_country_id')->nullable();
            $table->foreignId('order_detail_parent_id')->nullable();
            $table->foreignId('sender_receiver_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('service_id')->nullable();
            $table->foreignId('transaction_form_id')->nullable();
            $table->dateTime('order_detail_date')->nullable()->useCurrent();
            $table->string('order_detail_cause_name')->nullable();
            $table->decimal('order_detail_amount', 19, 6)->nullable();
            $table->string('order_detail_currency')->nullable();
            $table->decimal('converted_amount', 19, 6)->nullable();
            $table->string('converted_currency')->nullable();
            $table->string('order_detail_number')->nullable();
            $table->string('order_detail_response_id')->nullable();
            $table->integer('step')->default(0);
            $table->string('risk')->default(RiskProfile::Low->value)->nullable();
            $table->longText('notes')->nullable();
            $table->boolean('is_refundable')->nullable()->default(false);
            $table->json('order_detail_data')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('order_details');
    }
};
