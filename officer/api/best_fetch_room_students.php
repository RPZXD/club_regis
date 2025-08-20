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

    // Build student query conditions
    $conditions = ["Stu_status = '1'", "Stu_major = :level"];
    $params = ['level' => $level];
    
    if ($room !== '') {
        $conditions[] = "Stu_room = :room";
        $params['room'] = $room;
    }

    // Get all students in the specified level/room
    $studentQuery = "SELECT Stu_id, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room 
                     FROM student 
                     WHERE " . implode(' AND ', $conditions) . "
                     ORDER BY Stu_room, Stu_id";
    
    $studentStmt = $users->query($studentQuery, $params);
    $students = $studentStmt->fetchAll();

    // Get activity memberships for this year
    $memberQuery = "SELECT bm.student_id, bm.activity_id, ba.name as activity_name, bm.created_at
                    FROM best_members bm 
                    LEFT JOIN best_activities ba ON ba.id = bm.activity_id 
                    WHERE bm.year = :year";
    $memberStmt = $pdo->prepare($memberQuery);
    $memberStmt->execute(['year' => $year]);
    $memberMap = [];
    
    while ($member = $memberStmt->fetch()) {
        $memberMap[$member['student_id']] = [
            'activity' => $member['activity_name'] ?? 'ไม่ระบุ',
            'created_at' => $member['created_at']
        ];
    }

    // Combine student data with activity data
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

    echo json_encode(['ok' => true, 'data' => $result]);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'message' => 'Database error: ' . $e->getMessage(), 'data' => []]);
}
?>
