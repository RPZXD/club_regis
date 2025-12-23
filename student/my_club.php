<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'นักเรียน') {
    header('Location: ../login.php');
    exit;
}
$user = $_SESSION['user'];
$stu_id = $user['Stu_id'] ?? '';
$stu_major = isset($user['Stu_major']) ? $user['Stu_major'] : '';
$stu_grade = 'ม.' . $stu_major;

// Read configuration
$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

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

// Get clubs that student has registered
$sql = "SELECT c.*, m.year, m.term, m.created_at 
        FROM club_members m 
        JOIN clubs c ON m.club_id = c.club_id 
        WHERE m.student_id = :stu_id
        ORDER BY m.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['stu_id' => $stu_id]);
$myClubs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get advisor names
foreach ($myClubs as &$club) {
    $advisor = $dbUsers->query("SELECT Teach_name FROM teacher WHERE Teach_id = :id", ['id' => $club['advisor_teacher']])->fetch();
    $club['advisor_name'] = $advisor['Teach_name'] ?? $club['advisor_teacher'];
}
unset($club);

$pageTitle = 'ชุมนุมของฉัน';

ob_start();
include '../views/student/my_club.php';
$content = ob_get_clean();

include '../views/layouts/student_app.php';
?>
