<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode([]);
    exit;
}
require_once __DIR__.'/../../classes/DatabaseClub.php';
require_once __DIR__.'/../../classes/DatabaseUsers.php';
require_once __DIR__.'/../../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;

$club = new DatabaseClub();
$users = new DatabaseUsers();
$pdo = $club->getPDO();
$term = \TermPee::getCurrent();
$year = (int)$term->pee;

// Fetch member student IDs for this year from club DB
$stmt = $pdo->prepare('SELECT student_id FROM best_members WHERE year = :year');
$stmt->execute(['year' => $year]);
$levels = [];
while ($row = $stmt->fetch()) {
    $stu = $users->getStudentByUsername($row['student_id']);
    if (!$stu) continue;
    $key = 'ม.' . $stu['Stu_major'];
    if (!isset($levels[$key])) $levels[$key] = 0;
    $levels[$key]++;
}

// Normalize output order ม.1 .. ม.6
$ordered = [];
for ($i=1; $i<=6; $i++) {
    $k = 'ม.' . $i;
    if (isset($levels[$k])) {
        $ordered[] = ['level' => $k, 'count' => $levels[$k]];
    }
}
echo json_encode($ordered);
