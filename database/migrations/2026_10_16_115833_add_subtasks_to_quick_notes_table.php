<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quick_notes', function (Blueprint $table) {
            $table->json('subtasks')->nullable()->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('quick_notes', function (Blueprint $table) {
            $table->dropColumn('subtasks');
        });
    }
};
