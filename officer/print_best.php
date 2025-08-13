<?php
session_start();
// Allow only officer role to access
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    header('Location: ../login.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) { echo 'ไม่พบรหัสกิจกรรม'; exit; }

require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../models/BestActivity.php';
require_once __DIR__ . '/../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;
use App\Models\BestActivity;

$db = new DatabaseClub();
$pdo = $db->getPDO();
$bestModel = new BestActivity($pdo);
$dbUsers = new DatabaseUsers();

$termPee = \TermPee::getCurrent();
$year = $termPee->pee;

$activity = $bestModel->getById($id);
if (!$activity || intval($activity['year']) !== intval($year)) { echo 'ไม่พบกิจกรรมปีนี้'; exit; }

// fetch members and enrich with student data
$members = $bestModel->listMembers($id, $year);
$students = [];
foreach ($members as $row) {
    $stu = $dbUsers->getStudentByUsername($row['student_id']);
    $students[] = [
        'student_id' => $row['student_id'],
        'name' => $stu ? ($stu['Stu_pre'].$stu['Stu_name'].' '.$stu['Stu_sur']) : $row['student_id'],
        'Stu_major' => $stu['Stu_major'] ?? null,
        'Stu_room' => $stu['Stu_room'] ?? null,
        'Stu_no' => $stu['Stu_no'] ?? null,
    ];
}
// sort by grade, room, no
usort($students, function($a, $b) {
    $cmp = intval($a['Stu_major']) <=> intval($b['Stu_major']);
    if ($cmp !== 0) return $cmp;
    $cmp = intval($a['Stu_room']) <=> intval($b['Stu_room']);
    if ($cmp !== 0) return $cmp;
    return intval($a['Stu_no']) <=> intval($b['Stu_no']);
});

?><!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>พิมพ์รายชื่อเข้าร่วมกิจกรรม</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link href="https://fonts.googleapis.com/css2?family=TH+Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body, html {
            font-family: 'TH Sarabun', 'THSarabunNew', 'Sarabun', Arial, sans-serif !important;
            font-size: 14px;
        }
        @media print {
            @page { size: A4 portrait; margin: 5mm; }
            .no-print { display: none; }
            body { font-size: 14px !important; }
        }
        .print-border { border: 1px solid #000; }
        table.print-table th, table.print-table td { border: 1px solid #000; }
        table.print-table { border-collapse: collapse; width: 100%; }
        th, td { padding: 6px 8px; }
    </style>
    <script>
        function doPrint() { window.print(); }
    </script>
    <link rel="icon" href="data:,">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
</head>
<body class="bg-white">
    <div class="max-w-4xl mx-auto my-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <div class="text-2xl font-bold text-gray-800">แบบลงทะเบียนเข้าร่วมกิจกรรม</div>
                <div class="text-lg text-gray-700">กิจกรรม: <span class="font-semibold"><?php echo htmlspecialchars($activity['name']); ?></span></div>
                <div class="text-base text-gray-600">ปีการศึกษา: <?php echo htmlspecialchars($activity['year']); ?> | ระดับชั้นที่เปิดรับ: <?php echo htmlspecialchars($activity['grade_levels']); ?></div>
            </div>
            <button class="no-print px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700" onclick="doPrint()">
                พิมพ์
            </button>
        </div>

        <table class="print-table">
            <thead>
                <tr class="bg-gray-100">
                    <th class="text-center" style="width:5%">ลำดับ</th>
                    <th class="text-center" style="width:30%">ชื่อ - นามสกุล</th>
                    <th class="text-center" style="width:10%">ชั้น</th>
                    <th class="text-center" style="width:8%">เลขที่</th>
                    <th class="text-center" style="width:13%">ลงชื่อ</th>
                    <th class="text-center" style="width:10%">เวลามา</th>
                    <th class="text-center" style="width:13%">ลงชื่อ</th>
                    <th class="text-center" style="width:10%">เวลากลับ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $i => $stu): ?>
                    <?php
                        $className = '';
                        if ($stu['Stu_major'] !== null && $stu['Stu_room'] !== null) {
                            $className = 'ม.'.intval($stu['Stu_major']).'/'.intval($stu['Stu_room']);
                        }
                        $no = $stu['Stu_no'] !== null ? intval($stu['Stu_no']) : '';
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i+1; ?></td>
                        <td><?php echo htmlspecialchars($stu['name']); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($className); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($no); ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-gray-500">ไม่มีสมาชิกในกิจกรรมนี้</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        
    </div>
</body>
</html>
