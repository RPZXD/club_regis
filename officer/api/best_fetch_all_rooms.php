<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode(['ok' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__.'/../../classes/DatabaseUsers.php';

use App\DatabaseUsers;

try {
    $users = new DatabaseUsers();
    
    // Get all active students grouped by level and room
    $query = "SELECT DISTINCT Stu_major, Stu_room 
              FROM student 
              WHERE Stu_status = '1' 
              AND Stu_major BETWEEN 1 AND 6 
              AND Stu_room IS NOT NULL 
              AND Stu_room != ''
              ORDER BY Stu_major ASC, CAST(Stu_room AS UNSIGNED) ASC";
    
    $stmt = $users->query($query);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $levels = [];
    $roomsByLevel = [];
    
    foreach ($rooms as $room) {
        $level = (int)$room['Stu_major'];
        $roomNumber = trim($room['Stu_room']);
        
        if (!in_array($level, $levels)) {
            $levels[] = $level;
        }
        
        if (!isset($roomsByLevel[$level])) {
            $roomsByLevel[$level] = [];
        }
        
        if (!in_array($roomNumber, $roomsByLevel[$level])) {
            $roomsByLevel[$level][] = $roomNumber;
        }
    }
    
    // Sort levels and rooms
    sort($levels);
    foreach ($roomsByLevel as $level => $roomList) {
        sort($roomsByLevel[$level], SORT_NUMERIC);
    }
    
    echo json_encode([
        'ok' => true, 
        'data' => [
            'levels' => $levels,
            'roomsByLevel' => $roomsByLevel
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
