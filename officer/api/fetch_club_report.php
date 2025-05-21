<?php
// ตัวอย่าง API สำหรับส่งข้อมูลรายชุมนุม (ชื่อชุมนุม, ครูที่ปรึกษา, จำนวน ม.4, ม.5, ม.6, รวมทั้งสิ้น)
header('Content-Type: application/json');

// ใช้ Controller และ Models
require_once __DIR__ . '/../../classes/DatabaseUsers.php';
require_once __DIR__ . '/../../models/TermPee.php';
require_once __DIR__ . '/../../classes/DatabaseClub.php';
require_once __DIR__ . '/../../models/Club.php';
use App\DatabaseUsers;
use App\DatabaseClub;
use App\Models\TermPee;
use App\Models\Club;

// ใช้ Club model
$controller = new Club((new DatabaseClub())->getPDO());
$dbUsers = new DatabaseUsers();

$termPee = \TermPee::getCurrent();
$current_term = $termPee->term;
$current_year = $termPee->pee;

// ดึงข้อมูลชุมนุมทั้งหมด เฉพาะ term/year ปัจจุบัน
$clubs = $controller->getAll($current_term, $current_year);

// เตรียม array สำหรับนับตาม grade_levels (ม.1-ม.6)
$grade_level_keys = ["ม.1", "ม.2", "ม.3", "ม.4", "ม.5", "ม.6"];
$result = [];

// ดึง student ข้อมูลเดียวจบ (batch) สำหรับ lookup
$student_stmt = $dbUsers->query("SELECT Stu_id, Stu_major FROM student WHERE Stu_status = '1'");
$student_major_map = [];
while ($stu = $student_stmt->fetch()) {
    // Stu_major อาจเป็น 1-6 หรือ "ม.1"-"ม.6"
    $major = $stu['Stu_major'];
    if (in_array($major, ['1','2','3','4','5','6'])) {
        $major = "ม." . $major;
    }
    $student_major_map[$stu['Stu_id']] = $major;
}

foreach ($clubs as $club) {
    // หาครูที่ปรึกษา (ชื่อจริง)
    $advisor = $dbUsers->getTeacherByUsername($club['advisor_teacher']);
    $advisor_name = $advisor ? $advisor['Teach_name'] : $club['advisor_teacher'];

    // เตรียม array สำหรับนับตาม grade_levels (ม.1-ม.6)
    $grade_levels = array_fill_keys($grade_level_keys, 0);

    // ดึง student_id ทั้งหมดใน club_members ของชุมนุมนี้ (term/year ปัจจุบัน)
    $club_id = $club['club_id'];
    $pdo = $controller->getPDO();
    $stmt = $pdo->prepare("
        SELECT m.student_id
        FROM club_members m
        WHERE m.club_id = :club_id AND m.term = :term AND m.year = :year
    ");
    $stmt->execute(['club_id' => $club_id, 'term' => $current_term, 'year' => $current_year]);
    $total = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stu_id = $row['student_id'];
        $major = $student_major_map[$stu_id] ?? null;
        if ($major && isset($grade_levels[$major])) {
            $grade_levels[$major]++;
        }
        $total++;
    }

    $result[] = [
        'club_name' => $club['club_name'],
        'advisor' => $advisor_name,
        'grade_levels' => $grade_levels,
        'total_count' => $total
    ];
}

echo json_encode($result);

