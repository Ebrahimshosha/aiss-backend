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
        Schema::create('certificate_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('certificate_type_id')
                ->constrained('certificate_types')
                ->restrictOnDelete();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('company_name')->nullable();
            $table->string('country');
            $table->string('phone');
            $table->string('email');
            $table->text('notes')->nullable();

            $table->string('certificate_name');
            $table->decimal('amount', 10, 2);

            $table->string('order_status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->string('payment_reference')->nullable();

            $table->text('admin_notes')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_requests');
    }
};
