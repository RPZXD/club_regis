<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/TermPee.php';

class LoginController
{
    public function login($username, $password, $role)
    {
        $user = User::authenticate($username, $password, $role);
        if ($user === 'change_password') {
            // redirect ไปหน้าเปลี่ยนรหัสผ่าน
            $_SESSION['change_password_user'] = $username;
            header('Location: change_password.php');
            exit;
        }
        if ($user) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            if ($role === 'นักเรียน') {
                $_SESSION['user'] = [
                    'Stu_id' => $user['Stu_id'],
                    'Stu_pre' => $user['Stu_pre'],
                    'Stu_name' => $user['Stu_name'],
                    'Stu_sur' => $user['Stu_sur'],
                    'Stu_major' => $user['Stu_major'],
                    'Stu_room' => $user['Stu_room'],
                    'Stu_picture' => $user['Stu_picture'],
                ];
            } else {
                $_SESSION['user'] = [
                    'Teach_id' => $user['Teach_id'],
                    'Teach_name' => $user['Teach_name'],
                    'role_edoc' => $user['role_edoc'] ?? '',
                    'Teach_class' => $user['Teach_class'] ?? '',
                    'Teach_room' => $user['Teach_room'] ?? '',
                    'Teach_photo' => $user['Teach_photo'] ?? '',
                ];
            }
            // เพิ่มเก็บ term pee ลง session
            $termPee = \TermPee::getCurrent();
            $_SESSION['term'] = $termPee->term;
            $_SESSION['pee'] = $termPee->pee;
            return 'success';
        } else {
            return "ชื่อผู้ใช้, รหัสผ่าน หรือบทบาทไม่ถูกต้อง 🚫";
        }
    }

    /**
     * Get redirect URL based on user role
     */
    public function getRedirectUrl($role)
    {
        $redirectMap = [
            'ครู' => 'teacher/index.php',
            'นักเรียน' => 'student/index.php',
            'เจ้าหน้าที่' => 'officer/index.php',
            'admin' => 'officer/index.php',
            'ผู้บริหาร' => 'officer/index.php'
        ];
        return $redirectMap[$role] ?? 'index.php';
    }
}
