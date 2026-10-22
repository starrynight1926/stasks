<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $dupes = DB::table('departments')
            ->where('name', '-- Chưa phân --')
            ->orderBy('id')
            ->get();

        if ($dupes->count() <= 1) return;

        $keepId = $dupes->first()->id;
        $removeIds = $dupes->skip(1)->pluck('id')->all();

        DB::table('team_members')
            ->whereIn('department_id', $removeIds)
            ->update(['department_id' => $keepId]);

        DB::table('departments')->whereIn('id', $removeIds)->delete();
    }

    public function down(): void {}
};
