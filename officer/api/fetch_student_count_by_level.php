<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../classes/DatabaseUsers.php';
use App\DatabaseUsers;

$dbUsers = new DatabaseUsers();
$stmt = $dbUsers->query("SELECT Stu_major, COUNT(*) as cnt FROM student WHERE Stu_status = '1' GROUP BY Stu_major");
$result = [
    "ม.1" => 0,
    "ม.2" => 0,
    "ม.3" => 0,
    "ม.4" => 0,
    "ม.5" => 0,
    "ม.6" => 0
];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // รองรับ Stu_major เป็นตัวเลขหรือ "ม.1" "ม.2" ฯลฯ
    $major = $row['Stu_major'];
    $key = (strpos($major, 'ม.') === 0) ? $major : "ม." . $major;
    if (isset($result[$key])) {
        $result[$key] = intval($row['cnt']);
    }
}
echo json_encode($result);
