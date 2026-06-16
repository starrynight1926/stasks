<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('team_members')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('todo');
            $table->string('priority')->default('medium');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('progress', 5, 2)->default(0);
            $table->decimal('weight', 5, 2)->default(0);
            $table->string('visibility')->default('public');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
