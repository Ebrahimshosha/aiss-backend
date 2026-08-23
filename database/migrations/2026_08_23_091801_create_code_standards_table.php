<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('code_standards', function (Blueprint $table) {
            $table->id();

            $table->integer('user_id')->index();

            $table->string('title', 255);

            $table->string('slug', 255)->unique();

            $table->string('cover_image', 255);

            $table->string('inner_image', 255);

            $table->longText('content');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('code_standards');
    }
};