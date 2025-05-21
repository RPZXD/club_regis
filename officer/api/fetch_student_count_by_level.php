<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../classes/DatabaseUsers.php';
use App\DatabaseUsers;

$dbUsers = new DatabaseUsers();
$pdo = (new DatabaseUsers())->query("SELECT Stu_major, COUNT(*) as cnt FROM student WHERE Stu_status = '1' GROUP BY Stu_major");
$result = [
    "ม.1" => 0,
    "ม.2" => 0,
    "ม.3" => 0,
    "ม.4" => 0,
    "ม.5" => 0,
    "ม.6" => 0
];
while ($row = $pdo->fetch()) {
    $key = "ม." . $row['Stu_major'];
    if (isset($result[$key])) {
        $result[$key] = intval($row['cnt']);
    }
}
echo json_encode($result);
