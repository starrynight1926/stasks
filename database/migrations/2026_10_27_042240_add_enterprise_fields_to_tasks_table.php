<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('scope')->nullable()->after('visibility');
            $table->foreignId('company_id')->nullable()->after('scope')->constrained()->nullOnDelete();
            $table->boolean('is_cross_dept')->default(false)->after('company_id');
            $table->index('scope');
        });

        DB::table('tasks')->whereNull('scope')->update(['scope' => 'personal']);
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['scope']);
            $table->dropColumn(['scope', 'company_id', 'is_cross_dept']);
        });
    }
};
