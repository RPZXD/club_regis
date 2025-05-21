<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../classes/DatabaseUsers.php';
require_once __DIR__ . '/../../classes/DatabaseClub.php';
require_once __DIR__ . '/../../models/TermPee.php';

use App\DatabaseUsers;
use App\DatabaseClub;

// รับค่า GET
$level = $_GET['level'] ?? '';
$room = $_GET['room'] ?? '';

if (!$level || !$room) {
    echo json_encode([]);
    exit;
}

// แปลง "ม.1" => 1, "ม.2" => 2, ...
if (preg_match('/ม\.(\d+)/u', $level, $m)) {
    $level_num = $m[1];
} else {
    echo json_encode([]);
    exit;
}

$dbUsers = new DatabaseUsers();
$dbClub = new DatabaseClub();

// ดึงปี/เทอมปัจจุบัน
$termPee = \TermPee::getCurrent();
$current_term = $termPee->term;
$current_year = $termPee->pee;

// ดึงข้อมูลนักเรียนในชั้น/ห้องนี้
$sql = "SELECT Stu_id, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_no 
        FROM student 
        WHERE Stu_major = :level AND Stu_room = :room AND Stu_status = '1'
        ORDER BY Stu_no ASC";
$stmt = $dbUsers->query($sql, ['level' => $level_num, 'room' => $room]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลชุมนุมที่สมัคร (club_members)
$pdoClub = $dbClub->getPDO();
$clubStmt = $pdoClub->prepare("SELECT student_id, club_id FROM club_members WHERE term = :term AND year = :year");
$clubStmt->execute(['term' => $current_term, 'year' => $current_year]);
$clubMembers = [];
while ($row = $clubStmt->fetch(PDO::FETCH_ASSOC)) {
    $clubMembers[$row['student_id']] = $row['club_id'];
}

// ดึงชื่อชุมนุมทั้งหมด
$clubs = [];
$clubNameStmt = $pdoClub->query("SELECT club_id, club_name FROM clubs WHERE term = '$current_term' AND year = '$current_year'");
while ($row = $clubNameStmt->fetch(PDO::FETCH_ASSOC)) {
    $clubs[$row['club_id']] = $row['club_name'];
}

// clubs table structure:
// club_id, club_name, description, advisor_teacher, grade_levels, max_members, term, year, created_at, updated_at

// club_members table structure:
// id, student_id, club_id, term, year, created_at, updated_at

// เตรียมข้อมูลสำหรับตาราง
$result = [];
foreach ($students as $stu) {
    $student_id = $stu['Stu_id'];
    $club_id = $clubMembers[$student_id] ?? '';
    $club_name = $club_id && isset($clubs[$club_id]) ? $clubs[$club_id] : '-';
    $fullname = $stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur'];
    // ดึงชื่อครูที่ปรึกษาชุมนุม (advisor_teacher)
    $advisor = '-';
    if ($club_id && isset($clubs[$club_id])) {
        // ดึง advisor_teacher จาก clubs
        $clubInfoStmt = $pdoClub->prepare("SELECT advisor_teacher FROM clubs WHERE club_id = :club_id AND term = :term AND year = :year LIMIT 1");
        $clubInfoStmt->execute(['club_id' => $club_id, 'term' => $current_term, 'year' => $current_year]);
        $clubInfo = $clubInfoStmt->fetch(PDO::FETCH_ASSOC);
        if ($clubInfo && !empty($clubInfo['advisor_teacher'])) {
            // ดึงชื่อจริงของครูที่ปรึกษา
            $dbUsers = new \App\DatabaseUsers();
            $teacher = $dbUsers->getTeacherByUsername($clubInfo['advisor_teacher']);
            $advisor = $teacher ? ($teacher['Teach_name'] ?? $clubInfo['advisor_teacher']) : $clubInfo['advisor_teacher'];
        }
    }
    $result[] = [
        'student_id' => $student_id,
        'fullname' => $fullname,
        'level' => "ม." . $stu['Stu_major'],
        'room' => $stu['Stu_room'],
        'number' => $stu['Stu_no'],
        'club' => $club_name,
        'advisor' => $advisor
    ];
}

echo json_encode($result);
