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
        $currentYear = $term ? (int)$term->pee : (int)date('Y') + 543;
        $year = isset($_GET['year']) && (int)$_GET['year'] > 2000 ? (int)$_GET['year'] : $currentYear;
        $level = isset($_GET['level']) ? (int)$_GET['level'] : 1; // 1..6

        // Validate level input
        if ($level < 1 || $level > 6) {
            throw new InvalidArgumentException('Invalid level specified: ' . $level);
        }

        // Check connections
        if (!$pdo) {
            throw new Exception('Club database connection failed');
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

        // 1. Get activities for the requested year
        $activityStmt = $pdo->prepare("SELECT id, name FROM best_activities WHERE year = ? ORDER BY name");
        $activityStmt->execute([$year]);
        $activities = $activityStmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Get students in this level from student database
        $stuStmt = $users->query("SELECT Stu_id FROM student WHERE Stu_major = ? AND Stu_status = '1'", [$level]);
        $levelStuIds = $stuStmt ? $stuStmt->fetchAll(PDO::FETCH_COLUMN) : [];
        $levelStuSet = array_flip($levelStuIds);

        // 3. Get all registrations for this year
        $regStmt = $pdo->prepare("SELECT {$memberActivityColumn} as act_id, {$memberIdColumn} as stu_id FROM {$memberTable} WHERE year = ?");
        $regStmt->execute([$year]);
        $allRegs = $regStmt->fetchAll(PDO::FETCH_ASSOC);

        $actCounts = [];
        foreach ($allRegs as $r) {
            $aid = $r['act_id'];
            $sid = (string)$r['stu_id'];
            if (isset($levelStuSet[$sid]) || (empty($levelStuIds) && substr($sid, 0, 1) === (string)$level)) {
                $actCounts[$aid] = ($actCounts[$aid] ?? 0) + 1;
            }
        }

        $out = [];
        foreach ($activities as $activity) {
            $out[] = [
                'id' => (int)$activity['id'],
                'name' => $activity['name'],
                'count' => (int)($actCounts[$activity['id']] ?? 0)
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
