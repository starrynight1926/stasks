<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 150);
            $table->string('code', 50)->nullable();
            $table->string('description', 500)->nullable();
            $table->string('color', 7)->default('#3B82F6');
            $table->timestamps();
        });

        Schema::create('team_user', function (Blueprint $table) {
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_member_id')->constrained('team_members')->cascadeOnDelete();
            $table->string('role_in_team', 50)->nullable();
            $table->timestamps();
            $table->primary(['team_id', 'team_member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_user');
        Schema::dropIfExists('teams');
    }
};
