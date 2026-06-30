<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 50)->nullable()->unique();
            $table->string('tax_code', 50)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('logo', 255)->nullable();
            $table->string('description', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
