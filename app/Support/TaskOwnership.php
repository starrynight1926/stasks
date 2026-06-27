<?php

namespace App\Support;

use App\Models\Task;

class TaskOwnership
{
    public static function currentUser(): string
    {
        return (string) session('user_name', '');
    }

    public static function isOwner(Task $task): bool
    {
        $current = self::currentUser();
        if ($current === '') {
            return false;
        }
        if (empty($task->created_by)) {
            return true;
        }
        return $task->created_by === $current;
    }

    public static function abortIfNotOwner(Task $task): void
    {
        if (!self::isOwner($task)) {
            abort(403, 'Chỉ chủ topic (người tạo task) mới có quyền này.');
        }
    }
}
