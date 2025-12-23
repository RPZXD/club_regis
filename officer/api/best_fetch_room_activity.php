<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode(['ok' => false, 'message' => 'Unauthorized']);
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

    // Check which registration table exists
    $checkBestRegis = $pdo->query("SHOW TABLES LIKE 'best_regis'")->rowCount();
    $checkBestMembers = $pdo->query("SHOW TABLES LIKE 'best_members'")->rowCount();
    
    $regTable = '';
    if ($checkBestRegis > 0) $regTable = 'best_regis';
    elseif ($checkBestMembers > 0) $regTable = 'best_members';

    if (!$regTable) {
        echo json_encode(['ok' => true, 'rooms' => []]);
        exit;
    }

    // 1. Get all existing rooms for this level from student table
    $roomQuery = "SELECT DISTINCT Stu_room 
                  FROM student 
                  WHERE Stu_major = ? 
                  AND Stu_status = '1' 
                  AND Stu_room IS NOT NULL 
                  AND Stu_room != ''
                  ORDER BY CAST(Stu_room AS UNSIGNED) ASC";
    $roomStmt = $users->query($roomQuery, [$level]);
    $allRooms = $roomStmt->fetchAll(PDO::FETCH_COLUMN);
    
    $rooms = [];
    foreach ($allRooms as $r) {
        $rooms[$r] = 0;
    }

    // 2. Get registration counts per room for this year and level
    $stmt = $pdo->prepare("SELECT student_id FROM $regTable WHERE year = ?");
    $stmt->execute([$year]);
    $regIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($regIds)) {
        // Fetch room info for these registered students who are in the requested level
        $placeholders = str_repeat('?,', count($regIds) - 1) . '?';
        $countQuery = "SELECT Stu_room, COUNT(*) as count 
                       FROM student 
                       WHERE Stu_id IN ($placeholders) 
                       AND Stu_major = ? 
                       AND Stu_status = '1' 
                       GROUP BY Stu_room";
        
        $params = array_merge($regIds, [$level]);
        $countStmt = $users->query($countQuery, $params);
        $counts = $countStmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($counts as $row) {
            $rooms[$row['Stu_room']] = $row['count'];
        }
    }

    echo json_encode([
        'ok' => true,
        'rooms' => $rooms
    ]);

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
}
?>
