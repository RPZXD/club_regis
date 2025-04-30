<?php

require_once __DIR__ . '/../classes/DatabaseUsers.php';

class User
{
    // เพิ่ม mapping สำหรับ role ที่อนุญาต
    private static $allowedUserRoles = [
        'ครู' => ['T', 'ADM', 'VP', 'OF', 'DIR'],
        'เจ้าหน้าที่' => ['ADM', 'OF'],
        'ผู้บริหาร' => ['VP', 'DIR', 'ADM'],
        'admin' => ['ADM']
    ];

    public static function authenticate($username, $password, $role)
    {
        $db = new \App\DatabaseUsers();
        $user = $db->getTeacherByUsername($username);

        if ($user) {
            // ถ้า password ว่าง ให้ return 'change_password'
            if (empty($user['password'])) {
                return 'change_password';
            }
            if (
                password_verify($password, $user['password']) &&
                self::roleMatch($user['role_edoc'], $role)
            ) {
                return $user;
            }
        }
        return false;
    }

    // ตรวจสอบว่า role_edoc ของ user อยู่ในกลุ่ม role ที่เลือก
    private static function roleMatch($role_edoc, $role)
    {
        if (!isset(self::$allowedUserRoles[$role])) {
            return false;
        }
        return in_array($role_edoc, self::$allowedUserRoles[$role]);
    }
}
