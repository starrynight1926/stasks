<?php

namespace App\Support;

class Auth
{
    public static function isAdmin(): bool
    {
        return (bool) session('is_admin');
    }

    public static function memberId(): ?int
    {
        return session('member_id');
    }

    public static function branchId(): ?int
    {
        return session('branch_id');
    }

    public static function userName(): string
    {
        return (string) session('user_name', '');
    }

    public static function permissions(): array
    {
        return (array) session('permissions', []);
    }

    public static function can(string $key): bool
    {
        if (self::isAdmin()) return true;
        $perms = self::permissions();
        if (in_array('*', $perms, true)) return true;
        return in_array($key, $perms, true);
    }

    public static function cannot(string $key): bool
    {
        return !self::can($key);
    }
}
