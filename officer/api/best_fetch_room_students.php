<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode(['ok'=>false,'data'=>[]]);
    exit;
}
require_once __DIR__.'/../../classes/DatabaseClub.php';
require_once __DIR__.'/../../classes/DatabaseUsers.php';
require_once __DIR__.'/../../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;

$club = new DatabaseClub();
$pdo = $club->getPDO();
$users = new DatabaseUsers();
$term = \TermPee::getCurrent();
$year = (int)$term->pee;
$level = isset($_GET['level']) ? (int)$_GET['level'] : 1;
$room = isset($_GET['room']) ? trim($_GET['room']) : '';

// Get members joined with student and activity name
$sqlMembers = $pdo->prepare('SELECT bm.activity_id, bm.student_id, bm.created_at FROM best_members bm WHERE bm.year = :year');
$sqlMembers->execute(['year'=>$year]);
$actMap = [];
foreach ($pdo->query('SELECT id, name FROM best_activities WHERE year = '.(int)$year) as $a) {
    $actMap[(int)$a['id']] = $a['name'];
}

$res = [];
while ($m = $sqlMembers->fetch()) {
    $stu = $users->getStudentByUsername($m['student_id']);
    if (!$stu) continue;
    if ((int)$stu['Stu_major'] !== $level) continue;
    if ($room !== '' && (string)$stu['Stu_room'] !== $room) continue;
    $res[] = [
        'student_id' => $m['student_id'],
        'name' => $stu['Stu_pre'].$stu['Stu_name'].' '.$stu['Stu_sur'],
        'room' => 'ม.'.$stu['Stu_major'].'/'.$stu['Stu_room'],
        'activity' => $actMap[(int)$m['activity_id']] ?? ('กิจกรรม #'.$m['activity_id']),
        'created_at' => $m['created_at']
    ];
}

echo json_encode(['ok'=>true,'data'=>$res]);
