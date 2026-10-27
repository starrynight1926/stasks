<?php

namespace App\Support;

use App\Models\TeamMember;

class EnterpriseAccess
{
    public static function canUse(): bool
    {
        if (session('is_admin')) return true;
        $id = session('member_id');
        if (!$id) return false;
        $m = TeamMember::with('branch')->find($id);
        return $m && $m->branch && $m->branch->company_id !== null;
    }

    public static function isOn(): bool
    {
        return self::canUse() && (bool) session('enterprise_mode');
    }
}
