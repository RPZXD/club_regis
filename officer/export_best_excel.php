<?php
session_start();
// Allow only officer and admin role to access
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['เจ้าหน้าที่', 'admin'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../models/BestActivity.php';
require_once __DIR__ . '/../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;
use App\Models\BestActivity;

$termPee = TermPee::getCurrent();
$current_term = $termPee ? $termPee->term : 1;
$current_year = (int)($termPee ? $termPee->pee : (date('Y') + 543));

$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'] ?? [];
$schoolName = $global['nameschool'] ?? 'โรงเรียนพิชัย';

$db = new DatabaseClub();
$pdo = $db->getPDO();
$bestModel = new BestActivity($pdo);
$dbUsers = new DatabaseUsers();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$year = isset($_GET['year']) && intval($_GET['year']) > 0 ? intval($_GET['year']) : $current_year;

$targetActivities = [];
if ($id > 0) {
    $act = $bestModel->getById($id);
    if ($act) {
        $targetActivities[] = $act;
        $filename = "รายชื่อนักเรียน_BestForTeen_" . preg_replace('/[^\wก-๙\-]/u', '_', $act['name']) . "_{$year}.xls";
    } else {
        echo 'ไม่พบกิจกรรมที่ระบุ';
        exit;
    }
} else {
    // All activities in that year
    $targetActivities = $bestModel->listActivities($year);
    $filename = "รายชื่อนักเรียน_BestForTeen_ทุกกิจกรรม_ปีการศึกษา_{$year}.xls";
}

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
        body { font-family: 'Sarabun', 'TH Sarabun New', Tahoma, sans-serif; }
        table { border-collapse: collapse; width: 100%; font-size: 13px; margin-bottom: 25px; }
        th { 
            background-color: #059669; 
            color: #ffffff; 
            font-weight: bold; 
            border: 0.5pt solid #000000; 
            padding: 8px 6px; 
            text-align: center; 
            vertical-align: middle; 
        }
        td { 
            border: 0.5pt solid #000000; 
            padding: 6px 8px; 
            vertical-align: middle; 
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .title { font-size: 16px; font-weight: bold; text-align: center; }
        .subtitle { font-size: 13px; color: #333333; text-align: center; }
        .meta-info { font-size: 12px; color: #444444; }
        .bg-total { background-color: #d1fae5; font-weight: bold; }
        .text-num { mso-number-format:"\@"; text-align: center; }
    </style>
</head>
<body>
<?php
foreach ($targetActivities as $actIdx => $activity):
    $actId = $activity['id'];
    $members = $bestModel->listMembers($actId, $year);

    $students = [];
    foreach ($members as $row) {
        $stu = $dbUsers->getStudentByUsername($row['student_id']);
        $students[] = [
            'student_id' => $row['student_id'],
            'prefix'     => $stu['Stu_pre'] ?? '',
            'name'       => $stu['Stu_name'] ?? '',
            'surname'    => $stu['Stu_sur'] ?? '',
            'fullname'   => $stu ? ($stu['Stu_pre'].$stu['Stu_name'].' '.$stu['Stu_sur']) : $row['student_id'],
            'Stu_major'  => $stu['Stu_major'] ?? null,
            'Stu_room'   => $stu['Stu_room'] ?? null,
            'Stu_no'     => $stu['Stu_no'] ?? null,
            'created_at' => $row['created_at'] ?? ''
        ];
    }

    // Sort by Grade Level, Room, Number
    usort($students, function($a, $b) {
        $cmp = intval($a['Stu_major']) <=> intval($b['Stu_major']);
        if ($cmp !== 0) return $cmp;
        $cmp = intval($a['Stu_room']) <=> intval($b['Stu_room']);
        if ($cmp !== 0) return $cmp;
        return intval($a['Stu_no']) <=> intval($b['Stu_no']);
    });
?>
    <table>
        <tr>
            <td colspan="10" class="title"><?= htmlspecialchars($schoolName) ?></td>
        </tr>
        <tr>
            <td colspan="10" class="subtitle">แบบลงทะเบียน / รายชื่อนักเรียนเข้าร่วมกิจกรรม Best For Teen</td>
        </tr>
        <tr>
            <td colspan="6" class="meta-info">
                <b>กิจกรรม:</b> <?= htmlspecialchars($activity['name']) ?> | 
                <b>ปีการศึกษา:</b> <?= htmlspecialchars($year) ?> | 
                <b>ระดับชั้นที่เปิดรับ:</b> <?= htmlspecialchars($activity['grade_levels'] ?: 'ทุกระดับชั้น') ?>
            </td>
            <td colspan="4" class="text-right" style="font-size:11px; color:#666;">
                ส่งออกข้อมูลเมื่อ: <?= date('d/m/Y H:i:s') ?>
            </td>
        </tr>
        <tr>
            <td colspan="10" class="meta-info">
                <b>ยอดรับทั้งหมด:</b> <?= intval($activity['max_members']) ?> ที่นั่ง | 
                <b>จำนวนสมาชิกปัจจุบัน:</b> <?= count($students) ?> คน | 
                <b>คงเหลือ:</b> <?= max(0, intval($activity['max_members']) - count($students)) ?> ที่นั่ง
            </td>
        </tr>
        <tr><td colspan="10" style="border:none; height:8px;"></td></tr>
        <thead>
            <tr>
                <th style="width: 50px;">ลำดับ</th>
                <th style="width: 100px;">รหัสประจำตัว</th>
                <th style="width: 80px;">คำนำหน้า</th>
                <th style="width: 140px;">ชื่อ</th>
                <th style="width: 140px;">นามสกุล</th>
                <th style="width: 220px;">ชื่อ - นามสกุล</th>
                <th style="width: 80px;">ระดับชั้น</th>
                <th style="width: 60px;">ห้อง</th>
                <th style="width: 60px;">เลขที่</th>
                <th style="width: 150px;">เวลาลงทะเบียน</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($students)): ?>
                <tr>
                    <td colspan="10" class="text-center" style="color: #888; padding: 15px;">ไม่มีสมาชิกในกิจกรรมนี้</td>
                </tr>
            <?php else: ?>
                <?php foreach ($students as $i => $s): ?>
                    <?php
                        $levelStr = $s['Stu_major'] ? 'ม.' . intval($s['Stu_major']) : '-';
                        $roomStr = $s['Stu_room'] ? intval($s['Stu_room']) : '-';
                        $noStr = $s['Stu_no'] ? intval($s['Stu_no']) : '-';
                    ?>
                    <tr>
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td class="text-num"><?= htmlspecialchars($s['student_id']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($s['prefix']) ?></td>
                        <td class="text-left"><?= htmlspecialchars($s['name']) ?></td>
                        <td class="text-left"><?= htmlspecialchars($s['surname']) ?></td>
                        <td class="text-left"><?= htmlspecialchars($s['fullname']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($levelStr) ?></td>
                        <td class="text-center"><?= htmlspecialchars($roomStr) ?></td>
                        <td class="text-center"><?= htmlspecialchars($noStr) ?></td>
                        <td class="text-center"><?= htmlspecialchars($s['created_at'] ?: '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="bg-total">
                <td colspan="5" class="text-left">รวมจำนวนนักเรียนในกิจกรรมนี้</td>
                <td colspan="5" class="text-right" style="color:#059669; font-size:14px;"><?= count($students) ?> คน</td>
            </tr>
        </tfoot>
    </table>
    <br/>
<?php endforeach; ?>
</body>
</html>
