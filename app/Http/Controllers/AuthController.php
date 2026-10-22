<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /** Legacy admin login (giữ tương thích) */
    private const ADMIN_PASSWORD = 'tieuhoa195';
    private const ADMIN_NAME = 'Bảo Bảo Lucif';

    public function showLogin(Request $request)
    {
        if (session('authenticated')) {
            return redirect()->route('quick-tasks');
        }

        $token = $request->cookie('stasks_remember');
        if ($token) {
            try {
                $id = (int) Crypt::decryptString($token);
                $member = TeamMember::with('systemRole.permissions', 'branch')->find($id);
                if ($member && $member->username) {
                    $this->loginMember($member);
                    return redirect()->route('quick-tasks');
                }
            } catch (\Throwable $e) {
                Cookie::queue(Cookie::forget('stasks_remember'));
            }
        }

        return view('auth.login');
    }

    private function loginMember(TeamMember $member): void
    {
        $perms = $member->systemRole?->permissions->pluck('key')->all() ?? [];
        session([
            'authenticated' => true,
            'user_name'     => $member->name,
            'is_admin'      => false,
            'member_id'     => $member->id,
            'branch_id'     => $member->branch_id,
            'role_id'       => $member->role_id,
            'role_name'     => $member->systemRole?->name,
            'permissions'   => $perms,
            'must_change_password' => (bool) $member->must_change_password,
        ]);
    }

    public function login(Request $request)
    {
        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('password', '');

        // 1) Admin legacy: bỏ trống username + đúng admin password
        if (empty($username) && $password === self::ADMIN_PASSWORD) {
            session([
                'authenticated' => true,
                'user_name' => self::ADMIN_NAME,
                'is_admin' => true,
                'member_id' => null,
                'branch_id' => null,
                'role_id' => null,
                'permissions' => ['*'],
            ]);
            return redirect()->route('quick-tasks');
        }

        // 2) Member login: username + password match
        if ($username !== '') {
            $member = TeamMember::with('systemRole.permissions', 'branch')->where('username', $username)->first();
            if ($member && $member->password && Hash::check($password, $member->password)) {
                $this->loginMember($member);
                if ($request->boolean('remember')) {
                    Cookie::queue('stasks_remember', Crypt::encryptString((string) $member->id), 60 * 24 * 30);
                }
                return redirect()->route('quick-tasks');
            }
        }

        return back()->withErrors(['login' => 'Thông tin đăng nhập không đúng.'])->withInput();
    }

    public function logout()
    {
        session()->forget([
            'authenticated','user_name','is_admin','member_id','branch_id','role_id','role_name','permissions','must_change_password',
        ]);
        Cookie::queue(Cookie::forget('stasks_remember'));

        return redirect()->route('login');
    }

    public function showChangePassword()
    {
        if (!session('member_id')) {
            return redirect()->route('dashboard')->with('error', 'Chỉ tài khoản nhân sự mới đổi được mật khẩu tại đây.');
        }
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $memberId = session('member_id');
        if (!$memberId) {
            return redirect()->route('dashboard')->with('error', 'Tài khoản admin không hỗ trợ chức năng này.');
        }

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:4|max:200|confirmed',
        ]);

        $member = TeamMember::find($memberId);
        if (!$member || !Hash::check($validated['current_password'], $member->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $member->update([
            'password' => Hash::make($validated['new_password']),
            'must_change_password' => false,
        ]);

        session(['must_change_password' => false]);

        return redirect()->route('dashboard')->with('success', 'Đã đổi mật khẩu thành công.');
    }
}
