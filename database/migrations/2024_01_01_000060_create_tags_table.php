<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color', 7)->default('#3B82F6');
            $table->timestamps();
        });

        Schema::create('task_tag', function (Blueprint $table) {
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['task_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_tag');
        Schema::dropIfExists('tags');
    }
};
