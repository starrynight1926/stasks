<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quick_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id')->nullable()->index();
            $table->string('title');
            $table->date('iso');
            $table->string('status', 20)->default('todo');
            $table->string('project', 100)->nullable();
            $table->string('priority', 20)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['member_id', 'iso']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quick_notes');
    }
};
