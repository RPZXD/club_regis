<?php
session_start();
// Allow only officer role to access
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;

$dbUsers = new DatabaseUsers();
$dbClub = new DatabaseClub();

// Get active Term / Year
$termPee = \TermPee::getCurrent();
$current_term = $termPee->term;
$current_year = $termPee->pee;

$type = $_GET['type'] ?? 'school';
$level = $_GET['level'] ?? '';
$room = $_GET['room'] ?? '';
$club_id_filter = $_GET['club_id'] ?? '';

// If summary report is requested
if ($type === 'club_summary') {
    // Export Club Summary Table
    $pdoClub = $dbClub->getPDO();
    $clubStmt = $pdoClub->prepare("SELECT club_id, club_name, advisor_teacher, max_students FROM clubs WHERE term = :term AND year = :year ORDER BY club_name ASC");
    $clubStmt->execute(['term' => $current_term, 'year' => $current_year]);
    $clubs = $clubStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get student majors
    $studentStmt = $dbUsers->query("SELECT Stu_id, Stu_major FROM student WHERE Stu_status = '1'");
    $studentMajors = [];
    while ($s = $studentStmt->fetch(PDO::FETCH_ASSOC)) {
        $m = $s['Stu_major'];
        if (in_array($m, ['1','2','3','4','5','6'])) {
            $m = "ม." . $m;
        }
        $studentMajors[$s['Stu_id']] = $m;
    }

    $gradeKeys = ["ม.1", "ม.2", "ม.3", "ม.4", "ม.5", "ม.6"];
    $teacherCache = [];

    $filename = "สรุปยอดลงทะเบียนรายชุมนุม_ภาคเรียนที่_{$current_term}_ปีการศึกษา_{$current_year}.xls";
    
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . rawurlencode($filename) . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
    ?>
    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <style>
            table { border-collapse: collapse; width: 100%; font-family: 'Sarabun', 'TH Sarabun New', Tahoma, sans-serif; font-size: 13px; }
            th { background-color: #7c3aed; color: #ffffff; font-weight: bold; border: 0.5pt solid #000000; padding: 8px; text-align: center; vertical-align: middle; }
            td { border: 0.5pt solid #000000; padding: 6px 8px; vertical-align: middle; }
            .text-center { text-align: center; }
            .text-left { text-align: left; }
            .text-right { text-align: right; }
            .bg-total { background-color: #f3e8ff; font-weight: bold; }
            .title { font-size: 16px; font-weight: bold; text-align: center; }
            .subtitle { font-size: 13px; color: #555555; text-align: center; }
        </style>
    </head>
    <body>
        <table>
            <tr>
                <td colspan="10" class="title">สรุปรายงานยอดการลงทะเบียนรายชุมนุม</td>
            </tr>
            <tr>
                <td colspan="10" class="subtitle">โรงเรียนพิชัย ประจำภาคเรียนที่ <?= htmlspecialchars($current_term) ?> ปีการศึกษา <?= htmlspecialchars($current_year) ?></td>
            </tr>
            <tr>
                <td colspan="10" style="text-align:right; font-size:11px; color:#777;">ส่งออกข้อมูลเมื่อ: <?= date('d/m/Y H:i:s') ?></td>
            </tr>
            <tr><td colspan="10"></td></tr>
            <thead>
                <tr>
                    <th style="width: 50px;">ลำดับ</th>
                    <th style="width: 250px;">ชื่อชุมนุม</th>
                    <th style="width: 200px;">ครูที่ปรึกษา</th>
                    <th style="width: 60px;">ม.1</th>
                    <th style="width: 60px;">ม.2</th>
                    <th style="width: 60px;">ม.3</th>
                    <th style="width: 60px;">ม.4</th>
                    <th style="width: 60px;">ม.5</th>
                    <th style="width: 60px;">ม.6</th>
                    <th style="width: 90px;">รวมสมาชิก</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalSum = 0;
                $levelSums = array_fill_keys($gradeKeys, 0);

                foreach ($clubs as $idx => $club) {
                    $club_id = $club['club_id'];
                    $advisor_teacher = $club['advisor_teacher'];
                    if ($advisor_teacher) {
                        if (!isset($teacherCache[$advisor_teacher])) {
                            $t = $dbUsers->getTeacherByUsername($advisor_teacher);
                            $teacherCache[$advisor_teacher] = $t ? ($t['Teach_name'] ?? $advisor_teacher) : $advisor_teacher;
                        }
                        $advisor_name = $teacherCache[$advisor_teacher];
                    } else {
                        $advisor_name = '-';
                    }

                    // Count members
                    $mStmt = $pdoClub->prepare("SELECT student_id FROM club_members WHERE club_id = :cid AND term = :t AND year = :y");
                    $mStmt->execute(['cid' => $club_id, 't' => $current_term, 'y' => $current_year]);
                    $members = $mStmt->fetchAll(PDO::FETCH_COLUMN);

                    $clubLevelCounts = array_fill_keys($gradeKeys, 0);
                    $clubTotal = count($members);
                    $totalSum += $clubTotal;

                    foreach ($members as $stId) {
                        if (isset($studentMajors[$stId])) {
                            $maj = $studentMajors[$stId];
                            if (isset($clubLevelCounts[$maj])) {
                                $clubLevelCounts[$maj]++;
                                $levelSums[$maj]++;
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td class="text-center"><?= $idx + 1 ?></td>
                        <td class="text-left"><?= htmlspecialchars($club['club_name']) ?></td>
                        <td class="text-left"><?= htmlspecialchars($advisor_name) ?></td>
                        <?php foreach ($gradeKeys as $gk): ?>
                            <td class="text-center"><?= $clubLevelCounts[$gk] > 0 ? $clubLevelCounts[$gk] : '-' ?></td>
                        <?php endforeach; ?>
                        <td class="text-center" style="font-weight: bold;"><?= $clubTotal ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class="bg-total">
                    <td colspan="3" class="text-center">รวมทั้งสิ้น</td>
                    <?php foreach ($gradeKeys as $gk): ?>
                        <td class="text-center"><?= $levelSums[$gk] ?></td>
                    <?php endforeach; ?>
                    <td class="text-center" style="font-size:14px; color:#7c3aed;"><?= $totalSum ?></td>
                </tr>
            </tfoot>
        </table>
    </body>
    </html>
    <?php
    exit;
}

// Student List Export
// Normalize level filter
$level_num = '';
if ($level) {
    if (preg_match('/ม\.(\d+)/u', $level, $m)) {
        $level_num = $m[1];
    } else {
        $level_num = $level;
    }
}

// Build query
$where = ["Stu_status = '1'"];
$params = [];

if ($level_num) {
    $where[] = "Stu_major = :level";
    $params['level'] = $level_num;
}

if ($room) {
    $where[] = "Stu_room = :room";
    $params['room'] = $room;
}

$whereSql = implode(" AND ", $where);
$sql = "SELECT Stu_id, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_no 
        FROM student 
        WHERE $whereSql 
        ORDER BY CAST(Stu_major AS UNSIGNED) ASC, CAST(Stu_room AS UNSIGNED) ASC, CAST(Stu_no AS UNSIGNED) ASC";
$stmt = $dbUsers->query($sql, $params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch club registrations
$pdoClub = $dbClub->getPDO();
$clubStmt = $pdoClub->prepare("SELECT student_id, club_id FROM club_members WHERE term = :term AND year = :year");
$clubStmt->execute(['term' => $current_term, 'year' => $current_year]);
$clubMembers = [];
while ($row = $clubStmt->fetch(PDO::FETCH_ASSOC)) {
    $clubMembers[$row['student_id']] = $row['club_id'];
}

// Fetch all clubs info
$clubsInfo = [];
$clubNameStmt = $pdoClub->query("SELECT club_id, club_name, advisor_teacher FROM clubs WHERE term = '$current_term' AND year = '$current_year'");
while ($row = $clubNameStmt->fetch(PDO::FETCH_ASSOC)) {
    $clubsInfo[$row['club_id']] = [
        'club_name' => $row['club_name'],
        'advisor_teacher' => $row['advisor_teacher']
    ];
}

$advisorNameCache = [];

// Filter by club if club_id_filter is provided
if ($club_id_filter) {
    $students = array_filter($students, function($stu) use ($clubMembers, $club_id_filter) {
        $cid = $clubMembers[$stu['Stu_id']] ?? '';
        return $cid == $club_id_filter;
    });
}

// Determine title and filename
$reportTitle = "รายชื่อนักเรียนลงทะเบียนชุมนุม";
$filenamePrefix = "รายชื่อลงทะเบียนชุมนุม";

if ($level_num && $room) {
    $reportTitle .= " ชั้นมัธยมศึกษาปีที่ {$level_num}/{$room}";
    $filenamePrefix .= "_ม{$level_num}_{$room}";
} elseif ($level_num) {
    $reportTitle .= " ชั้นมัธยมศึกษาปีที่ {$level_num}";
    $filenamePrefix .= "_ม{$level_num}";
} else {
    $reportTitle .= " ทั้งโรงเรียน";
    $filenamePrefix .= "_ทั้งโรงเรียน";
}

$filename = "{$filenamePrefix}_ภาคเรียนที่_{$current_term}_ปีการศึกษา_{$current_year}.xls";

header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . rawurlencode($filename) . '"');
header('Pragma: no-cache');
header('Expires: 0');
echo "\xEF\xBB\xBF"; // UTF-8 BOM
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        table { border-collapse: collapse; width: 100%; font-family: 'Sarabun', 'TH Sarabun New', Tahoma, sans-serif; font-size: 13px; }
        th { background-color: #2563eb; color: #ffffff; font-weight: bold; border: 0.5pt solid #000000; padding: 7px; text-align: center; vertical-align: middle; }
        td { border: 0.5pt solid #000000; padding: 5px 7px; vertical-align: middle; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-id { mso-number-format:"\@"; text-align: center; }
        .status-registered { color: #059669; font-weight: bold; }
        .status-unregistered { color: #dc2626; font-weight: bold; }
        .title { font-size: 16px; font-weight: bold; text-align: center; }
        .subtitle { font-size: 13px; color: #555555; text-align: center; }
        .summary-box { background-color: #f8fafc; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="8" class="title"><?= htmlspecialchars($reportTitle) ?></td>
        </tr>
        <tr>
            <td colspan="8" class="subtitle">โรงเรียนพิชัย ประจำภาคเรียนที่ <?= htmlspecialchars($current_term) ?> ปีการศึกษา <?= htmlspecialchars($current_year) ?></td>
        </tr>
        <tr>
            <td colspan="8" style="text-align:right; font-size:11px; color:#777;">ส่งออกข้อมูลเมื่อ: <?= date('d/m/Y H:i:s') ?></td>
        </tr>
        <tr><td colspan="8"></td></tr>
        <thead>
            <tr>
                <th style="width: 50px;">ลำดับ</th>
                <th style="width: 50px;">เลขที่</th>
                <th style="width: 110px;">เลขประจำตัว</th>
                <th style="width: 220px;">ชื่อ - นามสกุล</th>
                <th style="width: 80px;">ชั้น/ห้อง</th>
                <th style="width: 220px;">ชุมนุมที่เลือก</th>
                <th style="width: 200px;">ครูที่ปรึกษาชุมนุม</th>
                <th style="width: 100px;">สถานะ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            $regCount = 0;
            $unregCount = 0;

            foreach ($students as $idx => $stu) {
                $count++;
                $student_id = $stu['Stu_id'];
                $fullname = $stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur'];
                $classRoom = "ม." . $stu['Stu_major'] . '/' . $stu['Stu_room'];
                
                $club_id = $clubMembers[$student_id] ?? '';
                $club_name = '-';
                $advisor_name = '-';
                $isRegistered = false;
                
                if ($club_id && isset($clubsInfo[$club_id])) {
                    $club_name = $clubsInfo[$club_id]['club_name'];
                    $advisor_teacher = $clubsInfo[$club_id]['advisor_teacher'];
                    if ($advisor_teacher) {
                        if (!isset($advisorNameCache[$advisor_teacher])) {
                            $teacher = $dbUsers->getTeacherByUsername($advisor_teacher);
                            $advisorNameCache[$advisor_teacher] = $teacher ? ($teacher['Teach_name'] ?? $advisor_teacher) : $advisor_teacher;
                        }
                        $advisor_name = $advisorNameCache[$advisor_teacher];
                    }
                    $isRegistered = true;
                    $regCount++;
                } else {
                    $unregCount++;
                }
                ?>
                <tr>
                    <td class="text-center"><?= $count ?></td>
                    <td class="text-center"><?= !empty($stu['Stu_no']) ? $stu['Stu_no'] : '-' ?></td>
                    <td class="text-id"><?= htmlspecialchars($student_id) ?></td>
                    <td class="text-left"><?= htmlspecialchars($fullname) ?></td>
                    <td class="text-center"><?= htmlspecialchars($classRoom) ?></td>
                    <td class="text-left"><?= htmlspecialchars($club_name) ?></td>
                    <td class="text-left"><?= htmlspecialchars($advisor_name) ?></td>
                    <td class="text-center <?= $isRegistered ? 'status-registered' : 'status-unregistered' ?>">
                        <?= $isRegistered ? 'ลงทะเบียนแล้ว' : 'ยังไม่ลงทะเบียน' ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr><td colspan="8"></td></tr>
            <tr class="summary-box">
                <td colspan="3" class="text-left" style="font-weight:bold;">สรุปภาพรวม</td>
                <td colspan="5" class="text-left">
                    นักเรียนทั้งหมด: <b><?= $count ?></b> คน &nbsp;&nbsp;|&nbsp;&nbsp; 
                    ลงทะเบียนแล้ว: <b style="color:#059669;"><?= $regCount ?></b> คน &nbsp;&nbsp;|&nbsp;&nbsp; 
                    ยังไม่ลงทะเบียน: <b style="color:#dc2626;"><?= $unregCount ?></b> คน
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
