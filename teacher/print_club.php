<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'ครู') {
    header('Location: ../login.php');
    exit;
}

$club_id = $_GET['club_id'] ?? '';
if (!$club_id) {
    echo "ไม่พบ club_id";
    exit;
}

// Data fetching logic
require_once __DIR__ . '/../models/Club.php';
require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;
use App\Models\Club;

$db = new DatabaseClub();
$pdo = $db->getPDO();
$clubModel = new Club($pdo);

$termPee = \TermPee::getCurrent();
$current_term = $termPee->term;
$current_year = $termPee->pee;

$club = $clubModel->getById($club_id, $current_term, $current_year);
if (!$club) {
    echo "ไม่พบข้อมูลชุมนุม";
    exit;
}

$dbUsers = new DatabaseUsers();
$advisor = $dbUsers->getTeacherByUsername($club['advisor_teacher']);
$advisor_name = $advisor ? $advisor['Teach_name'] : $club['advisor_teacher'];
$advisor_tel = $advisor ? $advisor['Teach_phone'] : $club['advisor_phone'];

$stmt = $pdo->prepare("SELECT * FROM club_members WHERE club_id = :club_id AND term = :term AND year = :year ORDER BY created_at ASC");
$stmt->execute(['club_id' => $club_id, 'term' => $current_term, 'year' => $current_year]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

$students = [];
foreach ($members as $row) {
    $stu = $dbUsers->getStudentByUsername($row['student_id']);
    $students[] = [
        'student_id' => $row['student_id'],
        'name' => $stu ? $stu['Stu_pre'].$stu['Stu_name'].' '.$stu['Stu_sur'] : '',
        'class_name' => $stu ? ('ม.'.$stu['Stu_major'].'/'.$stu['Stu_room'] ?? '') : '',
        'Stu_major' => $stu['Stu_major'] ?? null,
        'Stu_room' => $stu['Stu_room'] ?? null,
        'Stu_no' => $stu['Stu_no'] ?? null,
    ];
}

usort($students, function($a, $b) {
    $cmp = intval($a['Stu_major']) <=> intval($b['Stu_major']);
    if ($cmp !== 0) return $cmp;
    $cmp = intval($a['Stu_room']) <=> intval($b['Stu_room']);
    if ($cmp !== 0) return $cmp;
    return intval($a['Stu_no']) <=> intval($b['Stu_no']);
});

// Include the printable view (Doesn't use main layout)
include '../views/teacher/print_club.php';
?>
