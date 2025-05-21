<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../classes/DatabaseUsers.php';
require_once __DIR__ . '/../../classes/DatabaseClub.php';
require_once __DIR__ . '/../../models/TermPee.php';

use App\DatabaseUsers;
use App\DatabaseClub;

$level = $_GET['level'] ?? '';
if (!$level) {
    echo json_encode([]);
    exit;
}
if (preg_match('/ม\.(\d+)/u', $level, $m)) {
    $level_num = $m[1];
} else {
    echo json_encode([]);
    exit;
}

$dbUsers = new DatabaseUsers();
$dbClub = new DatabaseClub();
$termPee = \TermPee::getCurrent();
$current_term = $termPee->term;
$current_year = $termPee->pee;

// ดึงห้องทั้งหมดในชั้นนี้ (ใช้ฐานข้อมูล student)
$sql = "SELECT Stu_room FROM student WHERE Stu_major = :level AND Stu_status = '1' GROUP BY Stu_room ORDER BY Stu_room ASC";
$stmt = $dbUsers->query($sql, ['level' => $level_num]);
$rooms = $stmt->fetchAll(PDO::FETCH_COLUMN);

$result = [];
foreach ($rooms as $room) {
    // จำนวนนักเรียนในห้องนี้ (ใช้ฐานข้อมูล student)
    $sqlCount = "SELECT COUNT(*) as cnt FROM student WHERE Stu_major = :level AND Stu_room = :room AND Stu_status = '1'";
    $stmtCount = $dbUsers->query($sqlCount, ['level' => $level_num, 'room' => $room]);
    $student_count = $stmtCount->fetch()['cnt'];

    // หาชุมนุมที่มีสมาชิกมากที่สุดในห้องนี้ (join กับ student จากฐานข้อมูล student)
    $pdoClub = $dbClub->getPDO();
    $pdoStudent = $dbUsers->query("SELECT 1"); // dummy เพื่อให้แน่ใจว่าใช้ DatabaseUsers

    // ดึงรายชื่อ Stu_id ของนักเรียนในห้องนี้
    $stuIdStmt = $dbUsers->query(
        "SELECT Stu_id FROM student WHERE Stu_major = :level AND Stu_room = :room AND Stu_status = '1'",
        ['level' => $level_num, 'room' => $room]
    );
    $stuIds = $stuIdStmt->fetchAll(PDO::FETCH_COLUMN);

    $top_club = '-';
    $top_club_count = 0;

    if (!empty($stuIds)) {
        // สร้าง placeholders สำหรับ IN (...)
        $placeholders = implode(',', array_fill(0, count($stuIds), '?'));
        $sqlClub = "
            SELECT cm.club_id, COUNT(*) as cnt
            FROM club_members cm
            WHERE cm.student_id IN ($placeholders)
                AND cm.term = ?
                AND cm.year = ?
            GROUP BY cm.club_id
            ORDER BY cnt DESC
            LIMIT 1
        ";
        $params = array_merge($stuIds, [$current_term, $current_year]);
        $stmtClub = $pdoClub->prepare($sqlClub);
        $stmtClub->execute($params);
        $clubRow = $stmtClub->fetch(PDO::FETCH_ASSOC);

        if ($clubRow && $clubRow['club_id']) {
            // ดึงชื่อชุมนุม
            $clubNameStmt = $pdoClub->prepare("SELECT club_name FROM clubs WHERE club_id = :club_id AND term = :term AND year = :year");
            $clubNameStmt->execute([
                'club_id' => $clubRow['club_id'],
                'term' => $current_term,
                'year' => $current_year
            ]);
            $clubName = $clubNameStmt->fetch(PDO::FETCH_ASSOC);
            $top_club = $clubName ? $clubName['club_name'] : $clubRow['club_id'];
            $top_club_count = $clubRow['cnt'];
        }
    }

    $result[] = [
        'room' => $room,
        'student_count' => $student_count,
        'top_club' => $top_club,
        'top_club_count' => $top_club_count
    ];
}

echo json_encode($result);
