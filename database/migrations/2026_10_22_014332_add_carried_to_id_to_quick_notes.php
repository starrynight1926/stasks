<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quick_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('carried_to_id')->nullable()->after('subtasks')->index();
        });
    }

    public function down(): void
    {
        Schema::table('quick_notes', function (Blueprint $table) {
            $table->dropColumn('carried_to_id');
        });
    }
};
