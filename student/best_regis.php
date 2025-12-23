<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'นักเรียน') {
    header('Location: ../login.php');
    exit;
}
$user = $_SESSION['user'];
$stu_grade = 'ม.' . ($user['Stu_major'] ?? '');

// Load config
$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

// Check registration time settings
$best_setting_file = '../best_regis_setting.json';
$registration_open = true;
$message = '';
$alert_class = 'bg-green-50 border-green-200 text-green-800';

if (file_exists($best_setting_file)) {
    $best_setting = json_decode(file_get_contents($best_setting_file), true);
    if (isset($best_setting[$stu_grade])) {
        $regis_start = $best_setting[$stu_grade]['regis_start'] ?? '';
        $regis_end = $best_setting[$stu_grade]['regis_end'] ?? '';
        
        if ($regis_start && $regis_end) {
            $now = new DateTime();
            $start = new DateTime($regis_start);
            $end = new DateTime($regis_end);
            
            if ($now < $start) {
                $registration_open = false;
                $message = 'การสมัครกิจกรรม Best สำหรับ ' . $stu_grade . ' จะเปิดในวันที่ ' . $start->format('d/m/Y เวลา H:i น.');
                $alert_class = 'bg-yellow-50 border-yellow-200 text-yellow-800';
            } elseif ($now > $end) {
                $registration_open = false;
                $message = 'หมดเวลาการสมัครกิจกรรม Best สำหรับ ' . $stu_grade . ' แล้ว (ปิดรับสมัครเมื่อ ' . $end->format('d/m/Y เวลา H:i น.') . ')';
                $alert_class = 'bg-red-50 border-red-200 text-red-800';
            } else {
                $message = '🎉 กำลังเปิดรับสมัครกิจกรรม Best สำหรับ ' . $stu_grade . ' (ปิดรับสมัครวันที่ ' . $end->format('d/m/Y เวลา H:i น.') . ')';
                $alert_class = 'bg-green-50 border-green-200 text-green-800';
            }
        }
    }
}

$pageTitle = 'สมัคร Best For Teen';

ob_start();
include '../views/student/best_regis.php';
$content = ob_get_clean();

include '../views/layouts/student_app.php';
?>
