<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// เช็ค session และ role
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์ในการเข้าถึง']);
    exit;
}

try {
    $logs = [];
    
    // หาไฟล์ log ของเดือนปัจจุบันและเดือนก่อนหน้า
    $current_month = date('Y-m');
    $previous_month = date('Y-m', strtotime('-1 month'));
    
    $log_files = [
        "../../logs/best_regis_setting_{$current_month}.json",
        "../../logs/best_regis_setting_{$previous_month}.json"
    ];
    
    foreach ($log_files as $log_file) {
        if (file_exists($log_file)) {
            $file_logs = json_decode(file_get_contents($log_file), true);
            if (is_array($file_logs)) {
                $logs = array_merge($logs, $file_logs);
            }
        }
    }
    
    // เรียงลำดับตามเวลาจากใหม่ไปเก่า
    usort($logs, function($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });
    
    // จำกัดจำนวน log ที่แสดงไม่เกิน 20 รายการ
    $logs = array_slice($logs, 0, 20);
    
    echo json_encode([
        'success' => true,
        'logs' => $logs
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
