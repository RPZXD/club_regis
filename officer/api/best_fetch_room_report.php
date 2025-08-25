<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode(['ok' => false, 'message' => 'Unauthorized', 'data' => []]);
    exit;
}

require_once __DIR__.'/../../classes/DatabaseClub.php';
require_once __DIR__.'/../../classes/DatabaseUsers.php';
require_once __DIR__.'/../../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;

$startTime = microtime(true);

try {
    $club = new DatabaseClub();
    $pdo = $club->getPDO();
    $users = new DatabaseUsers();
    
    // Check connections
    if (!$pdo) {
        throw new Exception('Club database connection failed');
    }
    
    $term = \TermPee::getCurrent();
    if (!$term) {
        throw new Exception('Cannot get current term');
    }
    
    $year = (int)$term->pee;
    if (!$year || $year < 2020) {
        throw new Exception('Invalid year: ' . $year);
    }

    $pdo->beginTransaction();

    // Check if required tables exist
    $checkBestRegis = $pdo->query("SHOW TABLES LIKE 'best_regis'")->rowCount();
    if ($checkBestRegis === 0) {
        // Fallback to best_members table
        $tableName = 'best_members';
    } else {
        $tableName = 'best_regis';
    }

    // Get all registrations for this year
    $stmt = $pdo->prepare("SELECT student_id FROM {$tableName} WHERE year = ?");
    $stmt->execute([$year]);
    $studentIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $rooms = [];
    
    // Process each student ID to extract level and room
    foreach ($studentIds as $studentId) {
        // Extract level and room from student ID
        // Assuming format: LRR### where L=level, RR=room, ###=number
        if (strlen($studentId) >= 3) {
            $level = substr($studentId, 0, 1);
            $room = substr($studentId, 1, 2);
            
            // Remove leading zeros from room
            $room = (int)$room;
            
            if (is_numeric($level) && $level >= 1 && $level <= 6 && $room > 0) {
                $roomKey = 'ม.' . $level . '/' . $room;
                if (!isset($rooms[$roomKey])) {
                    $rooms[$roomKey] = 0;
                }
                $rooms[$roomKey]++;
            }
        }
    }

    $out = [];
    ksort($rooms);
    foreach ($rooms as $room => $count) { 
        $out[] = ['room' => $room, 'cnt' => $count]; 
    }

    $pdo->commit();
    
    $loadTime = (microtime(true) - $startTime) * 1000;
    
    echo json_encode([
        'ok' => true, 
        'data' => $out,
        'meta' => [
            'year' => $year,
            'table_used' => $tableName,
            'total_students' => count($studentIds),
            'total_rooms' => count($out),
            'loadTime' => round($loadTime, 2) . 'ms'
        ]
    ]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    $errorMessage = $e->getMessage();
    $errorFile = $e->getFile();
    $errorLine = $e->getLine();
    
    error_log("Best Room Report API Error: " . $errorMessage . " in " . $errorFile . " on line " . $errorLine);
    
    echo json_encode([
        'ok' => false, 
        'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูลรายห้อง',
        'data' => [],
        'debug' => [
            'error' => $errorMessage,
            'file' => basename($errorFile),
            'line' => $errorLine,
            'year' => isset($year) ? $year : 'unknown',
            'session_role' => $_SESSION['role'] ?? 'none',
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
}
?>
