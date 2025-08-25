<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: public, max-age=120'); // Cache for 2 minutes

if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode(['ok'=>false,'data'=>[],'message'=>'Unauthorized']);
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
        $term = \TermPee::getCurrent();
        $year = (int)$term->pee;
        $level = isset($_GET['level']) ? (int)$_GET['level'] : 1; // 1..6

        // Validate level input
        if ($level < 1 || $level > 6) {
            throw new InvalidArgumentException('Invalid level specified: ' . $level);
        }

        // Check connections
        if (!$pdo) {
            throw new Exception('Club database connection failed');
        }
        
        if (!$term) {
            throw new Exception('Cannot get current term');
        }
        
        if (!$year || $year < 2020) {
            throw new Exception('Invalid year: ' . $year);
        }

        $pdo->beginTransaction();

        // Check if required tables exist
        $checkBestActivities = $pdo->query("SHOW TABLES LIKE 'best_activities'")->rowCount();
        if ($checkBestActivities === 0) {
            throw new Exception('Table best_activities does not exist');
        }

        $checkBestRegis = $pdo->query("SHOW TABLES LIKE 'best_regis'")->rowCount();
        $checkBestMembers = $pdo->query("SHOW TABLES LIKE 'best_members'")->rowCount();
        
        if ($checkBestRegis > 0) {
            $memberTable = 'best_regis';
            $memberIdColumn = 'student_id';
            $memberActivityColumn = 'activity_id';
        } elseif ($checkBestMembers > 0) {
            $memberTable = 'best_members';
            $memberIdColumn = 'student_id';
            $memberActivityColumn = 'activity_id';
        } else {
            throw new Exception('No member registration table found (best_regis or best_members)');
        }

        // Simple approach: get activities first, then count registrations by level
        $activityStmt = $pdo->prepare("SELECT id, name FROM best_activities WHERE year = ? ORDER BY name");
        $activityStmt->execute([$year]);
        $activities = $activityStmt->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        
        foreach ($activities as $activity) {
            // Count registrations for this activity by students in the specified level
            // Assuming student IDs start with level number (e.g., 1xxxxx for ม.1)
            $countStmt = $pdo->prepare("
                SELECT COUNT(*) as count 
                FROM {$memberTable} 
                WHERE {$memberActivityColumn} = ? 
                AND year = ? 
                AND SUBSTRING({$memberIdColumn}, 1, 1) = ?
            ");
            
            $countStmt->execute([$activity['id'], $year, (string)$level]);
            $count = $countStmt->fetchColumn();
            
            $out[] = [
                'id' => (int)$activity['id'],
                'name' => $activity['name'],
                'count' => (int)$count
            ];
        }

        $pdo->commit();
        
        $loadTime = (microtime(true) - $startTime) * 1000;
        
        echo json_encode([
            'ok' => true,
            'data' => $out,
            'meta' => [
                'level' => $level,
                'year' => $year,
                'count' => count($out),
                'table_used' => $memberTable,
                'loadTime' => round($loadTime, 2) . 'ms'
            ]
        ], JSON_NUMERIC_CHECK);} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    $errorMessage = $e->getMessage();
    $errorFile = $e->getFile();
    $errorLine = $e->getLine();
    
    error_log("Best Level Activity API Error: " . $errorMessage . " in " . $errorFile . " on line " . $errorLine);
    
    echo json_encode([
        'ok' => false,
        'data' => [],
        'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล',
        'debug' => [
            'error' => $errorMessage,
            'file' => basename($errorFile),
            'line' => $errorLine,
            'level' => isset($level) ? $level : 'unknown',
            'year' => isset($year) ? $year : 'unknown',
            'session_role' => $_SESSION['role'] ?? 'none',
            'timestamp' => date('Y-m-d H:i:s'),
            'php_version' => PHP_VERSION
        ]
    ]);
}
?>
