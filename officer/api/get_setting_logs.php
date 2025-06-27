<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// เช็ค session และ role
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่มีสิทธิ์ในการเข้าถึง'
    ]);
    exit;
}

try {
    $logs = [];
    $logs_dir = '../../logs/';
    
    if (is_dir($logs_dir)) {
        $files = glob($logs_dir . 'regis_setting_*.json');
        
        foreach ($files as $file) {
            $file_logs = json_decode(file_get_contents($file), true);
            if ($file_logs) {
                $logs = array_merge($logs, $file_logs);
            }
        }
        
        // เรียงลำดับตามวันที่ล่าสุด
        usort($logs, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        // จำกัดเฉพาะ 50 รายการล่าสุด
        $logs = array_slice($logs, 0, 50);
    }
    
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
