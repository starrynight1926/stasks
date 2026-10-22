<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->string('level', 20)->default('member')->after('role');
            $table->unsignedBigInteger('parent_id')->nullable()->after('level')->index();
        });

        // Default fallback department
        $exists = DB::table('departments')->where('name', '-- Chưa phân --')->first();
        if (!$exists) {
            DB::table('departments')->insert([
                'name' => '-- Chưa phân --',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['level', 'parent_id']);
        });
    }
};
