<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#3B82F6');
            $table->string('head_name')->nullable();
            $table->string('head_avatar')->nullable();
            $table->integer('member_count')->default(0);
            $table->integer('active_projects')->default(0);
            $table->decimal('performance_score', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
