<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
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
            // Token format: "<id>|<sha256(id.appkey)>" — tránh phụ thuộc Crypt khi middleware thay đổi
            $parts = explode('|', (string) $token, 2);
            if (count($parts) === 2) {
                [$id, $sig] = $parts;
                $expected = hash_hmac('sha256', (string) $id, config('app.key'));
                if (hash_equals($expected, $sig)) {
                    $member = TeamMember::with('systemRole.permissions', 'branch')->find((int) $id);
                    if ($member && $member->username) {
                        $this->loginMember($member);
                        return redirect()->route('quick-tasks');
                    }
                }
            }
            Cookie::queue(Cookie::forget('stasks_remember'));
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
            'level'         => $member->level ?: 'member',
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
                $response = redirect()->route('quick-tasks');
                if ($request->boolean('remember')) {
                    $sig = hash_hmac('sha256', (string) $member->id, config('app.key'));
                    $response->withCookie(cookie(
                        'stasks_remember',
                        $member->id . '|' . $sig,
                        60 * 24 * 30
                    ));
                }
                return $response;
            }
        }

        return back()->withErrors(['login' => 'Thông tin đăng nhập không đúng.'])->withInput();
    }

    /** Admin giả lập đăng nhập dưới danh nghĩa 1 nhân sự */
    public function impersonate(TeamMember $teamMember)
    {
        if (!session('is_admin')) {
            abort(403, 'Chỉ admin được giả lập.');
        }

        $teamMember->loadMissing('systemRole.permissions', 'branch');

        // Giữ cờ gốc để biết đường thoát về admin
        session([
            'authenticated'   => true,
            'user_name'       => $teamMember->name,
            'is_admin'        => false,
            'member_id'       => $teamMember->id,
            'branch_id'       => $teamMember->branch_id,
            'role_id'         => $teamMember->role_id,
            'role_name'       => $teamMember->systemRole?->name,
            'level'           => $teamMember->level ?: 'member',
            'permissions'     => $teamMember->systemRole?->permissions->pluck('key')->all() ?? [],
            'must_change_password' => false,
            'impersonator_admin'   => true,
            'impersonated_name'    => $teamMember->name,
        ]);

        return redirect()->route('quick-tasks')->with('success', 'Đang giả lập: ' . $teamMember->name);
    }

    /** Thoát giả lập, trả về admin gốc */
    public function stopImpersonate()
    {
        if (!session('impersonator_admin')) {
            return redirect()->route('quick-tasks');
        }

        session([
            'authenticated' => true,
            'user_name'     => self::ADMIN_NAME,
            'is_admin'      => true,
            'member_id'     => null,
            'branch_id'     => null,
            'role_id'       => null,
            'role_name'     => null,
            'level'         => null,
            'permissions'   => ['*'],
            'must_change_password' => false,
        ]);
        session()->forget(['impersonator_admin', 'impersonated_name']);

        return redirect()->route('teams')->with('success', 'Đã thoát chế độ giả lập.');
    }

    public function logout()
    {
        session()->forget([
            'authenticated','user_name','is_admin','member_id','branch_id','role_id','role_name','level','permissions','must_change_password',
            'impersonator_admin','impersonated_name','enterprise_mode',
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
