<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->timestamp('done_at')->nullable()->after('progress');
            $table->timestamp('cancelled_at')->nullable()->after('done_at');
            $table->string('cancel_reason', 500)->nullable()->after('cancelled_at');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['done_at', 'cancelled_at', 'cancel_reason']);
        });
    }
};
