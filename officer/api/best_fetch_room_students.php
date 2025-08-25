<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: public, max-age=180'); // Cache for 3 minutes

if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode(['ok'=>false,'message'=>'Unauthorized','data'=>[]]);
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
    $level = isset($_GET['level']) ? (int)$_GET['level'] : 1;
    $room = isset($_GET['room']) ? trim($_GET['room']) : '';

    // Validate inputs
    if ($level < 1 || $level > 6) {
        throw new InvalidArgumentException('Invalid level specified');
    }

    // Start transactions for both connections
    $pdo->beginTransaction();
    
    // Build conditions for student query
    $conditions = ["s.Stu_status = '1'", "s.Stu_major = ?"];
    $studentParams = [$level];
    
    if ($room !== '') {
        $conditions[] = "s.Stu_room = ?";
        $studentParams[] = $room;
    }

    // Optimized student query with better indexing hints
    $studentQuery = "SELECT s.Stu_id, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room 
                     FROM student s
                     WHERE " . implode(' AND ', $conditions) . "
                     ORDER BY s.Stu_room, s.Stu_id
                     LIMIT 1000"; // Prevent memory issues with large datasets
    
    $studentStmt = $users->query($studentQuery, $studentParams);
    $students = $studentStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($students)) {
        $pdo->commit();
        echo json_encode([
            'ok' => true, 
            'data' => [],
            'meta' => [
                'level' => $level,
                'room' => $room ?: 'ทุกห้อง',
                'count' => 0,
                'loadTime' => round((microtime(true) - $startTime) * 1000, 2) . 'ms'
            ]
        ]);
        exit;
    }

    // Get student IDs for batch lookup - optimized
    $studentIds = array_column($students, 'Stu_id');
    $chunkSize = 100; // Process in chunks to avoid SQL limits
    $memberMap = [];
    
    // Process student IDs in chunks for better performance
    foreach (array_chunk($studentIds, $chunkSize) as $chunk) {
        $placeholders = str_repeat('?,', count($chunk) - 1) . '?';
        
        // Use best_regis table instead of best_members for better performance  
        $memberQuery = "SELECT br.student_id, ba.name as activity_name, br.created_at
                        FROM best_regis br 
                        LEFT JOIN best_activities ba ON ba.id = br.activity_id AND ba.year = br.year
                        WHERE br.year = ? AND br.student_id IN ($placeholders)";
        
        $memberParams = array_merge([$year], $chunk);
        $memberStmt = $pdo->prepare($memberQuery);
        $memberStmt->execute($memberParams);
        
        // Build activity map efficiently
        while ($member = $memberStmt->fetch(PDO::FETCH_ASSOC)) {
            $memberMap[$member['student_id']] = [
                'activity' => $member['activity_name'] ?? 'ไม่ระบุกิจกรรม',
                'created_at' => $member['created_at'] ? date('d/m/Y H:i', strtotime($member['created_at'])) : null
            ];
        }
    }

    // Build result efficiently with better memory management
    $result = [];
    $studentsWithActivity = 0;
    $studentsWithoutActivity = 0;
    
    foreach ($students as $student) {
        $memberInfo = $memberMap[$student['Stu_id']] ?? null;
        $hasActivity = $memberInfo !== null;
        
        if ($hasActivity) {
            $studentsWithActivity++;
        } else {
            $studentsWithoutActivity++;
        }
        
        $result[] = [
            'student_id' => $student['Stu_id'],
            'name' => trim($student['Stu_pre'] . $student['Stu_name'] . ' ' . $student['Stu_sur']),
            'room' => 'ม.' . $student['Stu_major'] . '/' . $student['Stu_room'],
            'activity' => $memberInfo ? $memberInfo['activity'] : 'ไม่ได้สมัคร',
            'created_at' => $memberInfo ? $memberInfo['created_at'] : '-',
            'has_activity' => $hasActivity
        ];
    }

    $pdo->commit();
    
    $loadTime = (microtime(true) - $startTime) * 1000;
    
    echo json_encode([
        'ok' => true, 
        'data' => $result,
        'meta' => [
            'level' => $level,
            'room' => $room ?: 'ทุกห้อง',
            'totalStudents' => count($result),
            'withActivity' => $studentsWithActivity,
            'withoutActivity' => $studentsWithoutActivity,
            'loadTime' => round($loadTime, 2) . 'ms'
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    error_log("Best Room Students API Error: " . $e->getMessage());
    
    echo json_encode([
        'ok' => false, 
        'message' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล', 
        'data' => [],
        'debug' => ($_ENV['APP_DEBUG'] ?? false) ? $e->getMessage() : null
    ]);
}
?>
