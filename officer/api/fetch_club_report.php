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

$result = [];
foreach ($clubs as $club) {
    // หาครูที่ปรึกษา (ชื่อจริง)
    $advisor = $dbUsers->getTeacherByUsername($club['advisor_teacher']);
    $advisor_name = $advisor ? $advisor['Teach_name'] : $club['advisor_teacher'];

    // เตรียม array สำหรับนับตาม grade_levels (ใช้เฉพาะ "ม.4", "ม.5", "ม.6" เท่านั้น)
    $grade_levels = [
        "ม.4" => 0,
        "ม.5" => 0,
        "ม.6" => 0
    ];
    if (!empty($club['grade_levels'])) {
        $grades = array_map('trim', explode(',', $club['grade_levels']));
        foreach ($grades as $g) {
            // แปลงเลข 4/5/6 เป็น "ม.4"/"ม.5"/"ม.6"
            if (in_array($g, ['4', '5', '6'])) {
                $key = "ม." . $g;
            } else {
                $key = $g;
            }
            if (!isset($grade_levels[$key])) {
                $grade_levels[$key] = 0;
            }
        }
    }

    // นับสมาชิกแต่ละระดับชั้น (Stu_major จาก student ที่อยู่อีกฐาน)
    $club_id = $club['club_id'];
    // แก้ไขการเข้าถึง pdo: เพิ่ม public getter ใน Club model แล้วใช้แทน
    $pdo = $controller->getPDO();
    $stmt = $pdo->prepare("
        SELECT m.student_id
        FROM club_members m
        WHERE m.club_id = :club_id AND m.term = :term AND m.year = :year
    ");
    $stmt->execute(['club_id' => $club_id, 'term' => $current_term, 'year' => $current_year]);
    $total = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stu = $dbUsers->getStudentByUsername($row['student_id']);
        if (!$stu) continue;
        $major = $stu['Stu_major'];
        // แปลงเลข 4/5/6 เป็น "ม.4"/"ม.5"/"ม.6"
        $level_key = (in_array($major, ['4', '5', '6'])) ? "ม." . $major : $major;
        if (isset($grade_levels[$level_key])) {
            $grade_levels[$level_key]++;
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

