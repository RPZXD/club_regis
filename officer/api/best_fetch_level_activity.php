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
$level = isset($_GET['level']) ? (int)$_GET['level'] : 1; // 1..6

// Get counts per activity for students in the selected level
$sql = "SELECT bm.activity_id, COUNT(*) AS cnt
        FROM best_members bm
        JOIN student s ON s.Stu_id = bm.student_id
        WHERE bm.year = :year AND s.Stu_major = :level
        GROUP BY bm.activity_id";
// cross-db join is not guaranteed; fallback approach
try {
    $stmt = $users->query($sql, ['year'=>$year,'level'=>$level]);
    $counts = [];
    while ($r = $stmt->fetch()) { $counts[$r['activity_id']] = (int)$r['cnt']; }
} catch (\Exception $e) {
    // fallback: fetch members then enrich
    $stmt2 = $pdo->prepare('SELECT activity_id, student_id FROM best_members WHERE year = :year');
    $stmt2->execute(['year'=>$year]);
    $counts = [];
    while ($m = $stmt2->fetch()) {
        $stu = $users->getStudentByUsername($m['student_id']);
        if (!$stu || (int)$stu['Stu_major'] !== $level) continue;
        $aid = (int)$m['activity_id'];
        if (!isset($counts[$aid])) $counts[$aid] = 0;
        $counts[$aid]++;
    }
}

// Get activity names
$acts = $pdo->query('SELECT id, name FROM best_activities WHERE year = '.(int)$year)->fetchAll();
$out = [];
foreach ($acts as $a) {
    $aid = (int)$a['id'];
    $out[] = [ 'id'=>$aid, 'name'=>$a['name'], 'count'=> ($counts[$aid] ?? 0) ];
}

echo json_encode(['ok'=>true,'data'=>$out]);
