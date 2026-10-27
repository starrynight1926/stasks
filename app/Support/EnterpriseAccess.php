<?php

namespace App\Support;

use App\Models\Task;
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

    /** Full edit rights on a task: creator, assignee, admin, or (classic) in visible set. */
    public static function canEditTask(Task $t): bool
    {
        if (session('is_admin')) return true;
        $meId = (int) session('member_id');
        if (!$meId) return false;
        if ((int) $t->created_by === $meId) return true;
        if ((int) $t->assignee_id === $meId) return true;
        $me = TeamMember::find($meId);
        if (!$me) return false;
        $visible = $me->visibleMemberIds();
        return in_array($t->created_by, $visible, true)
            || in_array($t->assignee_id, $visible, true);
    }

    /** Cross-dept assignee can touch status of their subtask only (returns true for status edits). */
    public static function canEditSubtaskStatus(Task $sub): bool
    {
        if (self::canEditTask($sub)) return true;
        $meId = (int) session('member_id');
        if (!$meId || !$sub->parent_id) return false;
        $parent = $sub->parent;
        return $parent && $parent->is_cross_dept && (int) $sub->assignee_id === $meId;
    }
}
