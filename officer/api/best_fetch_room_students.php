<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode(['ok'=>false,'message'=>'Unauthorized','data'=>[]]);
    exit;
}

require_once __DIR__.'/../../classes/DatabaseClub.php';
require_once __DIR__.'/../../classes/DatabaseUsers.php';
require_once __DIR__.'/../../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;

try {
    $club = new DatabaseClub();
    $pdo = $club->getPDO();
    $users = new DatabaseUsers();
    
    $term = \TermPee::getCurrent();
    $year = (int)$term->pee;
    $level = isset($_GET['level']) ? (int)$_GET['level'] : 1;
    $room = isset($_GET['room']) ? trim($_GET['room']) : '';

    // Build conditions for student query
    $conditions = ["s.Stu_status = '1'", "s.Stu_major = ?"];
    $studentParams = [$level];
    
    if ($room !== '') {
        $conditions[] = "s.Stu_room = ?";
        $studentParams[] = $room;
    }

    // Optimized single query with JOIN to get all data at once
    // Use the users connection for student data and club connection for best activities
    $studentQuery = "SELECT s.Stu_id, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room 
                     FROM student s
                     WHERE " . implode(' AND ', $conditions) . "
                     ORDER BY s.Stu_room, s.Stu_id";
    
    $studentStmt = $users->query($studentQuery, $studentParams);
    $students = $studentStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($students)) {
        echo json_encode(['ok' => true, 'data' => []]);
        exit;
    }

    // Get student IDs for batch lookup
    $studentIds = array_column($students, 'Stu_id');
    $placeholders = str_repeat('?,', count($studentIds) - 1) . '?';
    
    // Single optimized query for best activities
    $memberQuery = "SELECT bm.student_id, ba.name as activity_name, bm.created_at
                    FROM best_members bm 
                    LEFT JOIN best_activities ba ON ba.id = bm.activity_id 
                    WHERE bm.year = ? AND bm.student_id IN ($placeholders)";
    
    $memberParams = array_merge([$year], $studentIds);
    $memberStmt = $pdo->prepare($memberQuery);
    $memberStmt->execute($memberParams);
    
    // Create activity map
    $memberMap = [];
    while ($member = $memberStmt->fetch(PDO::FETCH_ASSOC)) {
        $memberMap[$member['student_id']] = [
            'activity' => $member['activity_name'] ?? 'ไม่ระบุ',
            'created_at' => $member['created_at']
        ];
    }

    // Build result efficiently
    $result = [];
    foreach ($students as $student) {
        $memberInfo = $memberMap[$student['Stu_id']] ?? null;
        
        $result[] = [
            'student_id' => $student['Stu_id'],
            'name' => $student['Stu_pre'] . $student['Stu_name'] . ' ' . $student['Stu_sur'],
            'room' => 'ม.' . $student['Stu_major'] . '/' . $student['Stu_room'],
            'activity' => $memberInfo ? $memberInfo['activity'] : 'ไม่ได้สมัคร',
            'created_at' => $memberInfo ? $memberInfo['created_at'] : null,
            'has_activity' => $memberInfo !== null
        ];
    }

    echo json_encode(['ok' => true, 'data' => $result], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'message' => 'Database error: ' . $e->getMessage(), 'data' => []]);
}
?>
