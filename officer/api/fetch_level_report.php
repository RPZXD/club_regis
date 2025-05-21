<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/DatabaseClub.php';
require_once __DIR__ . '/../../classes/DatabaseUsers.php';
require_once __DIR__ . '/../../models/Club.php';
require_once __DIR__ . '/../../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;
use App\Models\Club;

// รับค่าระดับชั้น (level) เช่น "ม.4"
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

// ดึง student_id ทั้งหมดใน club_members ที่ตรง term/year แล้วไปดึงข้อมูล student จากฐาน users
$stmt = $pdo->prepare("
    SELECT DISTINCT m.student_id
    FROM club_members m
    WHERE m.term = :term AND m.year = :year
");
$stmt->execute(['term' => $current_term, 'year' => $current_year]);
$students = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $stu = $dbUsers->getStudentByUsername($row['student_id']);
    if ($stu && isset($stu['Stu_major']) && isset($stu['Stu_room'])) {
        if ('ม.' . $stu['Stu_major'] === $level) {
            $students[] = [
                'student_id' => $row['student_id'],
                'room' => $stu['Stu_room']
            ];
        }
    }
}

// หา room ทั้งหมดในระดับชั้นนี้
$rooms = [];
foreach ($students as $stu) {
    if (!in_array($stu['room'], $rooms)) {
        $rooms[] = $stu['room'];
    }
}
sort($rooms);

$result = [];
foreach ($rooms as $room) {
    // หา student_id ทั้งหมดในห้องนี้
    $student_ids = [];
    foreach ($students as $stu) {
        if ($stu['room'] == $room) {
            $student_ids[] = $stu['student_id'];
        }
    }

    // นับจำนวนนักเรียนในห้องนี้
    $total_students = count($student_ids);

    // หาชุมนุมที่มีสมาชิกมากที่สุดในห้องนี้
    $club_counts = [];
    if ($total_students > 0) {
        $inQuery = implode(',', array_fill(0, count($student_ids), '?'));
        $sql = "SELECT club_id, COUNT(*) as cnt FROM club_members WHERE student_id IN ($inQuery) AND term = ? AND year = ? GROUP BY club_id";
        $stmt2 = $pdo->prepare($sql);
        $params = array_merge($student_ids, [$current_term, $current_year]);
        $stmt2->execute($params);
        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            $club_counts[$row2['club_id']] = intval($row2['cnt']);
        }
    }

    $top_club_id = null;
    $top_club_count = 0;
    foreach ($club_counts as $cid => $cnt) {
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

echo json_encode($result);
