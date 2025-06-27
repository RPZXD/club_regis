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
    // รับข้อมูลจาก POST
    $regis_start = $_POST['regis_start'] ?? [];
    $regis_end = $_POST['regis_end'] ?? [];
    
    // ตรวจสอบข้อมูล
    $levels = ['ม.1', 'ม.2', 'ม.3', 'ม.4', 'ม.5', 'ม.6'];
    $settings = [];
    
    foreach ($levels as $level) {
        if (isset($regis_start[$level]) && isset($regis_end[$level])) {
            $start = $regis_start[$level];
            $end = $regis_end[$level];
            
            // ตรวจสอบรูปแบบวันที่
            if (!empty($start) && !empty($end)) {
                $start_time = strtotime($start);
                $end_time = strtotime($end);
                
                if ($start_time === false || $end_time === false) {
                    throw new Exception("รูปแบบวันที่ไม่ถูกต้องสำหรับ {$level}");
                }
                
                if ($end_time <= $start_time) {
                    throw new Exception("วันปิดรับสมัครต้องมากกว่าวันเปิดรับสมัครสำหรับ {$level}");
                }
                
                $settings[$level] = [
                    'regis_start' => date('Y-m-d H:i:s', $start_time),
                    'regis_end' => date('Y-m-d H:i:s', $end_time)
                ];
            } else {
                // ถ้าไม่มีข้อมูล ให้เก็บเป็นค่าว่าง
                $settings[$level] = [
                    'regis_start' => '',
                    'regis_end' => ''
                ];
            }
        }
    }
    
    // บันทึกลงไฟล์ JSON
    $json_file = '../../regis_setting.json';
    $json_data = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if (file_put_contents($json_file, $json_data) === false) {
        throw new Exception('ไม่สามารถบันทึกไฟล์ได้');
    }
    
    // สร้าง log การเปลี่ยนแปลง
    $log_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user' => $_SESSION['username'],
        'action' => 'update_regis_setting',
        'data' => $settings
    ];
    
    $log_file = '../../logs/regis_setting_' . date('Y-m') . '.json';
    
    // สร้างโฟลเดอร์ logs ถ้ายังไม่มี
    if (!file_exists('../../logs')) {
        mkdir('../../logs', 0755, true);
    }
    
    // อ่าน log เดิม
    $existing_logs = [];
    if (file_exists($log_file)) {
        $existing_logs = json_decode(file_get_contents($log_file), true) ?: [];
    }
    
    // เพิ่ม log ใหม่
    $existing_logs[] = $log_data;
    
    // บันทึก log
    file_put_contents($log_file, json_encode($existing_logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo json_encode([
        'success' => true,
        'message' => 'บันทึกการตั้งค่าเรียบร้อยแล้ว'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
