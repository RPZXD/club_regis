<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Login extends Model
{
    public static function attempt($username, $password, $role)
    {
        if ($role === 'student') {
            // ตรวจสอบ username กับ Stu_id และ password กับ Stu_password
            $user = DB::connection('phichaia_student')
                ->table('student')
                ->where('Stu_id', $username)
                ->first();
            if ($user && isset($user->Stu_password) && $user->Stu_password == $password) {
                return $user;
            }
        } else {
            // ตรวจสอบ username กับ Teach_id และ password กับ password
            $user = DB::connection('phichaia_student')
                ->table('teacher')
                ->where('Teach_id', $username)
                ->first();
            if ($user && isset($user->password) && password_verify($password, $user->password)) {
                return $user;
            }
        }
        return null;
    }
}
