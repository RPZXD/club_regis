<?php

require_once '../models/User.php';
require_once '../models/Logger.php';
$databaseFile = '../config/Database.php';
require_once '../utils/Utils.php';

class LoginController {
    /**
     * Convert absolute file path to relative URL path for frontend usage.
     * @param string $path Absolute file path
     * @return string Relative URL path
     */
    public static function pathToUrl($path) {
        return str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath($path));
    }

    public function login($post) {
        $logger = new Logger('../logs/login.json');
        $username = filter_var($post['txt_username_email'], FILTER_SANITIZE_STRING);
        $password = filter_var($post['txt_password'], FILTER_SANITIZE_STRING);
        $role = filter_var($post['txt_role'], FILTER_SANITIZE_STRING);

        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $sessionId = session_id();
        $accessTime = date("c");

        $allowed_roles = ['Admin', 'Teacher', 'Officer', 'Director', 'Parent', 'Student'];
        if (!in_array($role, $allowed_roles)) {
            $role = 'Teacher';
        }

        $studentDb = new \Database("phichaia_student");
        $studentConn = $studentDb->getConnection();
        $user = new \User($studentConn);

        $user->setUsername($username);
        $user->setPassword($password);

        if ($role === 'Student') {
            if ($user->studentNotExists()) {
                $logger->log([
                    'username' => $username,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'session_id' => $sessionId,
                    'access_time' => $accessTime,
                    'status' => 'error',
                    'message' => 'ไม่มีชื่อนักเรียนนี้'
                ]);
                (new SweetAlert2('ไม่มีชื่อนักเรียนนี้', 'error', 'login.php'))->renderAlert();
            } else {
                if ($user->verifyStudentPassword()) {
                    $stuStatus = $user->getUserRoleStudent();
                    if ($stuStatus == 1) {
                        $_SESSION['user'] = $username;
                        $_SESSION['Student_login'] = $_SESSION['user'];
                        $logger->log([
                            'username' => $username,
                            'ip_address' => $ipAddress,
                            'user_agent' => $userAgent,
                            'session_id' => $sessionId,
                            'access_time' => $accessTime,
                            'status' => 'success',
                            'message' => 'ลงชื่อเข้าสู่ระบบเรียบร้อย'
                        ]);
                        (new SweetAlert2('ลงชื่อเข้าสู่ระบบเรียบร้อย', 'success', 'student/index.php'))->renderAlert();
                    } else {
                        $logger->log([
                            'username' => $username,
                            'ip_address' => $ipAddress,
                            'user_agent' => $userAgent,
                            'session_id' => $sessionId,
                            'access_time' => $accessTime,
                            'status' => 'error',
                            'message' => 'นักเรียนนี้ไม่มีสถานะปกติ'
                        ]);
                        (new SweetAlert2('นักเรียนนี้ไม่มีสถานะปกติ', 'error', 'login.php'))->renderAlert();
                    }
                } else {
                    $logger->log([
                        'username' => $username,
                        'ip_address' => $ipAddress,
                        'user_agent' => $userAgent,
                        'session_id' => $sessionId,
                        'access_time' => $accessTime,
                        'status' => 'error',
                        'message' => 'พาสเวิร์ดไม่ถูกต้อง'
                    ]);
                    (new SweetAlert2('พาสเวิร์ดไม่ถูกต้อง', 'error', 'login.php'))->renderAlert();
                }
            }
        } else {
            if ($user->userNotExists()) {
                $logger->log([
                    'username' => $username,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'session_id' => $sessionId,
                    'access_time' => $accessTime,
                    'status' => 'error',
                    'message' => 'ไม่มีชื่อผู้ใช้นี้'
                ]);
                (new SweetAlert2('ไม่มีชื่อผู้ใช้นี้', 'error', 'login.php'))->renderAlert();
            } else {
                if ($user->verifyPassword()) {
                    $userRole = $user->getUserRole();
                    $allowedUserRoles = [
                        'Teacher' => ['T', 'ADM', 'VP', 'OF', 'DIR'],
                        'Officer' => ['ADM', 'OF'],
                        'Director' => ['VP', 'DIR', 'ADM'],
                        'Admin' => ['ADM']
                    ];
                    if (in_array($userRole, $allowedUserRoles[$role])) {
                        $_SESSION['user'] = $username;
                        $_SESSION[$role . '_login'] = $_SESSION['user'];
                        $logger->log([
                            'username' => $username,
                            'ip_address' => $ipAddress,
                            'user_agent' => $userAgent,
                            'session_id' => $sessionId,
                            'access_time' => $accessTime,
                            'status' => 'success',
                            'message' => 'ลงชื่อเข้าสู่ระบบเรียบร้อย'
                        ]);
                        (new SweetAlert2('ลงชื่อเข้าสู่ระบบเรียบร้อย', 'success', strtolower($role) . '/index.php'))->renderAlert();
                    } else {
                        $logger->log([
                            'username' => $username,
                            'ip_address' => $ipAddress,
                            'user_agent' => $userAgent,
                            'session_id' => $sessionId,
                            'access_time' => $accessTime,
                            'status' => 'error',
                            'message' => 'บทบาทผู้ใช้ไม่ถูกต้อง'
                        ]);
                        (new SweetAlert2('บทบาทผู้ใช้ไม่ถูกต้อง', 'error', 'login.php'))->renderAlert();
                    }
                } else {
                    $logger->log([
                        'username' => $username,
                        'ip_address' => $ipAddress,
                        'user_agent' => $userAgent,
                        'session_id' => $sessionId,
                        'access_time' => $accessTime,
                        'status' => 'error',
                        'message' => 'พาสเวิร์ดไม่ถูกต้อง'
                    ]);
                    (new SweetAlert2('พาสเวิร์ดไม่ถูกต้อง', 'error', 'login.php'))->renderAlert();
                }
            }
        }
    }
}
?>
