<?php

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        $company = Company::firstOrCreate(
            ['name' => 'Công ty mặc định'],
            ['code' => 'main', 'description' => 'Công ty được tạo tự động khi cài đặt hệ thống.']
        );
        Branch::whereNull('company_id')->update(['company_id' => $company->id]);
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
