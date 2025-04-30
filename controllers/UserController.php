<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Logger.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../utils/Utils.php';

class UserController {
    public function logout() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $studentDb = new \Database("phichaia_student");
        $studentConn = $studentDb->getConnection();

        $user = new \User($studentConn); // ใช้ User model แทน
        $logger = new \Logger('../logs/logout.json');

        // Get role if available (เหมือน login)
        $role = null;
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];
        } elseif (isset($_SESSION['txt_role'])) {
            $role = $_SESSION['txt_role'];
        } elseif (isset($_SESSION['user'])) {
            $userData = $user->userData($_SESSION['user']); // ใช้ User model
            if (isset($userData['role'])) {
                $role = $userData['role'];
            } elseif (isset($userData['role_std'])) {
                $role = $userData['role_std'];
            } elseif (isset($userData['Teach_type'])) {
                $map = [
                    'T' => 'Teacher',
                    'ADM' => 'Admin',
                    'VP' => 'Director',
                    'OF' => 'Officer',
                    'DIR' => 'Director'
                ];
                $role = $map[$userData['Teach_type']] ?? $userData['Teach_type'];
            } elseif (isset($userData['role_name'])) {
                $role = $userData['role_name'];
            }
        }

        if (isset($_SESSION['user'])) {
            $logger->log([
                "user_id" => $_SESSION['user'],
                "role" => $role,
                "ip_address" => $_SERVER['REMOTE_ADDR'],
                "user_agent" => $_SERVER['HTTP_USER_AGENT'],
                "access_time" => date("c"),
                "url" => $_SERVER['REQUEST_URI'],
                "method" => $_SERVER['REQUEST_METHOD'],
                "status_code" => 200,
                "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
                "action_type" => "logout",
                "session_id" => session_id(),
                "message" => "Logout successful"
            ]);
            session_destroy();
            // แจ้งเตือน logout สำเร็จ
            (new \SweetAlert2('คุณได้ออกจากระบบแล้ว', 'success', 'login.php'))->renderAlert();
            exit;
        } else {
            $logger->log([
                "user_id" => null,
                "role" => null,
                "ip_address" => $_SERVER['REMOTE_ADDR'],
                "user_agent" => $_SERVER['HTTP_USER_AGENT'],
                "access_time" => date("c"),
                "url" => $_SERVER['REQUEST_URI'],
                "method" => $_SERVER['REQUEST_METHOD'],
                "status_code" => 400,
                "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
                "action_type" => "logout_attempt",
                "session_id" => session_id(),
                "message" => "Logout attempted without an active session"
            ]);
            // แจ้งเตือนกรณีไม่มี session
            (new \SweetAlert2('ไม่มี session สำหรับออกจากระบบ', 'error', 'login.php'))->renderAlert();
            exit;
        }


    }
}