<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/DatabaseClub.php';
require_once __DIR__ . '/../../classes/DatabaseUsers.php';
require_once __DIR__ . '/../../models/Club.php';
require_once __DIR__ . '/../../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;
use App\Models\Club;

$level = $_GET['level'] ?? '';
if (!$level) {
    echo json_encode([]);
    exit;
}

$termPee = \TermPee::getCurrent();
$current_term = $termPee->term;
$current_year = $termPee->pee;

$dbClub = new DatabaseClub();
$dbUsers = new DatabaseUsers();
$pdo = $dbClub->getPDO();
$clubModel = new Club($pdo);

// ดึง student_id, Stu_room, Stu_major ล่วงหน้าทั้งหมดในระดับชั้นนี้ (batch)
$stmt = $dbUsers->query(
    "SELECT Stu_id, Stu_room, Stu_major FROM student WHERE Stu_status = '1' AND Stu_major = ?",
    [str_replace('ม.', '', $level)]
);
$studentMap = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $studentMap[$row['Stu_id']] = $row['Stu_room'];
}

// ดึง club_members เฉพาะ student_id ที่อยู่ในระดับชั้นนี้และ term/year นี้
if (empty($studentMap)) {
    echo json_encode([]);
    exit;
}
$studentIds = array_keys($studentMap);
$inQuery = implode(',', array_fill(0, count($studentIds), '?'));
$params = array_merge($studentIds, [$current_term, $current_year]);
$sql = "SELECT student_id, club_id FROM club_members WHERE student_id IN ($inQuery) AND term = ? AND year = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$roomClubCount = []; // [room][club_id] => count
$roomStudentCount = []; // [room] => count
foreach ($stmt as $row) {
    $stu_id = $row['student_id'];
    $club_id = $row['club_id'];
    $room = $studentMap[$stu_id];
    if (!$room) continue;
    if (!isset($roomClubCount[$room])) $roomClubCount[$room] = [];
    if (!isset($roomClubCount[$room][$club_id])) $roomClubCount[$room][$club_id] = 0;
    $roomClubCount[$room][$club_id]++;
    if (!isset($roomStudentCount[$room])) $roomStudentCount[$room] = [];
    $roomStudentCount[$room][$stu_id] = true;
}

// สร้างผลลัพธ์
$result = [];
foreach ($roomClubCount as $room => $clubCounts) {
    $total_students = isset($roomStudentCount[$room]) ? count($roomStudentCount[$room]) : 0;
    $top_club_id = null;
    $top_club_count = 0;
    foreach ($clubCounts as $cid => $cnt) {
        if ($cnt > $top_club_count) {
            $top_club_id = $cid;
            $top_club_count = $cnt;
        }
    }
    $top_club_name = '';
    if ($top_club_id) {
        $club = $clubModel->getById($top_club_id, $current_term, $current_year);
        $top_club_name = $club ? $club['club_name'] : '';
    }
    $result[] = [
        'room' => $room,
        'student_count' => $total_students,
        'top_club' => $top_club_name,
        'top_club_count' => $top_club_count
    ];
}

// sort room
usort($result, function($a, $b) {
    return $a['room'] <=> $b['room'];
});

echo json_encode($result);
