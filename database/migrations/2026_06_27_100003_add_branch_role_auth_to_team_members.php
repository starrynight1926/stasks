<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('role_id')->nullable()->after('branch_id')->constrained()->nullOnDelete();
            $table->string('username', 80)->nullable()->after('role_id')->unique();
            $table->string('password')->nullable()->after('username');
            $table->boolean('must_change_password')->default(false)->after('password');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('project_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn(['username', 'password', 'must_change_password']);
        });
    }
};
