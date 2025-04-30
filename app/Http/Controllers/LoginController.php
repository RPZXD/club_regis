<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Login;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'role' => 'required',
        ]);

        $user = Login::attempt($request->username, $request->password, $request->role);

        if ($user) {
            // สามารถเพิ่ม session หรือ redirect ตามต้องการ
            // ตัวอย่าง: session(['user' => $user, 'role' => $request->role]);
            return redirect('/dashboard')->with('success', 'เข้าสู่ระบบสำเร็จ');
        } else {
            return back()->withErrors(['login' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'])->withInput();
        }
    }
}
