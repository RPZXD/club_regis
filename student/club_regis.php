<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'นักเรียน') {
    header('Location: ../login.php');
    exit;
}
$user = $_SESSION['user'];
$stu_major = isset($user['Stu_major']) ? $user['Stu_major'] : '';
$stu_grade = 'ม.' . $stu_major;

// Load config
$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

// Load registration settings
date_default_timezone_set('Asia/Bangkok');
$regisSetting = json_decode(file_get_contents('../regis_setting.json'), true);
$stuGradeKey = $stu_grade;
if (isset($regisSetting[$stuGradeKey])) {
    $regisStart = isset($regisSetting[$stuGradeKey]['regis_start']) ? strtotime($regisSetting[$stuGradeKey]['regis_start']) : null;
    $regisEnd = isset($regisSetting[$stuGradeKey]['regis_end']) ? strtotime($regisSetting[$stuGradeKey]['regis_end']) : null;
} else {
    $regisStart = null;
    $regisEnd = null;
}
$now = time();
$regisOpen = ($regisStart && $regisEnd && $now >= $regisStart && $now <= $regisEnd);

// Load clubs from database
require_once('../classes/DatabaseClub.php');
require_once('../models/Club.php');
require_once('../classes/DatabaseUsers.php');

use App\DatabaseClub;
use App\Models\Club;
use App\DatabaseUsers;

$db = new DatabaseClub();
$pdo = $db->getPDO();
$clubModel = new Club($pdo);
$dbUsers = new DatabaseUsers();

// Get clubs for student's grade level
$allClubs = $clubModel->getAll();
$clubs = [];
foreach ($allClubs as $club) {
    $grades = array_map('trim', explode(',', $club['grade_levels']));
    if (in_array($stu_grade, $grades)) {
        $currentMembers = $db->getCurrentMembers($club['club_id']);
        $club['current_members_count'] = count($currentMembers);

        $advisor = $dbUsers->query("SELECT Teach_name FROM teacher WHERE Teach_id = :id", ['id' => $club['advisor_teacher']])->fetch();
        $club['advisor_teacher_name'] = $advisor ? $advisor['Teach_name'] : $club['advisor_teacher'];

        $clubs[] = $club;
    }
}

$pageTitle = 'สมัครชุมนุม';

ob_start();
include '../views/student/club_regis.php';
$content = ob_get_clean();

include '../views/layouts/student_app.php';
?>
