<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// เช็ค session และ role
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์ในการเข้าถึง']);
    exit;
}

try {
    // ตรวจสอบข้อมูลที่ส่งมา
    if (!isset($_POST['best_regis_start']) || !isset($_POST['best_regis_end'])) {
        throw new Exception('ข้อมูลไม่ครบถ้วน');
    }
    
    $best_regis_start = $_POST['best_regis_start'];
    $best_regis_end = $_POST['best_regis_end'];
    $levels = ['ม.1', 'ม.2', 'ม.3', 'ม.4', 'ม.5', 'ม.6'];
    
    // สร้างข้อมูลการตั้งค่า
    $best_setting_data = [];
    
    foreach ($levels as $level) {
        $start_datetime = isset($best_regis_start[$level]) && !empty($best_regis_start[$level]) ? 
                         date('Y-m-d H:i:s', strtotime($best_regis_start[$level])) : '';
        $end_datetime = isset($best_regis_end[$level]) && !empty($best_regis_end[$level]) ? 
                       date('Y-m-d H:i:s', strtotime($best_regis_end[$level])) : '';
        
        // ตรวจสอบว่าวันปิดรับสมัครต้องมากกว่าวันเปิดรับสมัคร
        if (!empty($start_datetime) && !empty($end_datetime)) {
            if (strtotime($end_datetime) <= strtotime($start_datetime)) {
                throw new Exception("วันปิดรับสมัคร Best ของ {$level} ต้องมากกว่าวันเปิดรับสมัคร");
            }
        }
        
        $best_setting_data[$level] = [
            'regis_start' => $start_datetime,
            'regis_end' => $end_datetime
        ];
    }
    
    // บันทึกข้อมูลลงไฟล์ JSON
    $json_data = json_encode($best_setting_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents('../../best_regis_setting.json', $json_data) === false) {
        throw new Exception('ไม่สามารถบันทึกข้อมูลได้');
    }
    
    // บันทึก log การเปลี่ยนแปลง
    $log_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user' => $_SESSION['username'],
        'action' => 'update_best_setting',
        'data' => $best_setting_data
    ];
    
    // อ่านข้อมูล log เก่า
    $logs_file = '../../logs/best_regis_setting_' . date('Y-m') . '.json';
    $logs_dir = dirname($logs_file);
    
    // สร้างโฟลเดอร์ logs หากยังไม่มี
    if (!is_dir($logs_dir)) {
        mkdir($logs_dir, 0755, true);
    }
    
    $existing_logs = [];
    if (file_exists($logs_file)) {
        $existing_logs = json_decode(file_get_contents($logs_file), true) ?: [];
    }
    
    // เพิ่ม log ใหม่
    array_unshift($existing_logs, $log_data);
    
    // จำกัดจำนวน log ไม่เกิน 100 รายการ
    if (count($existing_logs) > 100) {
        $existing_logs = array_slice($existing_logs, 0, 100);
    }
    
    // บันทึก log
    $log_json = json_encode($existing_logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($logs_file, $log_json);
    
    echo json_encode([
        'success' => true, 
        'message' => 'บันทึกการตั้งค่า Best สำเร็จ'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>
