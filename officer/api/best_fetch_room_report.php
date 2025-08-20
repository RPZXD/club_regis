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

    $stmt = $pdo->prepare('SELECT student_id FROM best_members WHERE year = :year');
    $stmt->execute(['year' => $year]);
    $rooms = [];
    while ($row = $stmt->fetch()) {
        $stu = $users->getStudentByUsername($row['student_id']);
        if (!$stu) continue;
        $key = 'ม.' . $stu['Stu_major'] . '/' . $stu['Stu_room'];
        if (!isset($rooms[$key])) $rooms[$key] = 0;
        $rooms[$key]++;
    }

    $out = [];
    ksort($rooms);
    foreach ($rooms as $room => $cnt) { $out[] = ['room' => $room, 'cnt' => $cnt]; }
    echo json_encode(['ok' => true, 'data' => $out]);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
