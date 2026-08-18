<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issued_certificates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('certificate_request_id')
                ->unique()
                ->constrained('certificate_requests')
                ->restrictOnDelete();

            $table->foreignId('certificate_type_id')
                ->constrained('certificate_types')
                ->restrictOnDelete();

            $table->string('code')->unique();

            $table->string('certificate_name');
            $table->string('holder_name');

            $table->date('issue_date');
            $table->date('expiry_date')->nullable();

            $table->string('status')->default('active');

            $table->string('file_path');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issued_certificates');
    }
};
