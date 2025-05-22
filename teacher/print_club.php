<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'ครู') {
    header('Location: ../login.php');
    exit;
}

$club_id = $_GET['club_id'] ?? '';
if (!$club_id) {
    echo "ไม่พบ club_id";
    exit;
}

// ดึงข้อมูลชุมนุมและสมาชิก
require_once __DIR__ . '/../models/Club.php';
require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;
use App\Models\Club;

$db = new DatabaseClub();
$pdo = $db->getPDO();
$clubModel = new Club($pdo);

// ดึงเทอม/ปีปัจจุบัน
$termPee = \TermPee::getCurrent();
$current_term = $termPee->term;
$current_year = $termPee->pee;

// ข้อมูลชุมนุม
$club = $clubModel->getById($club_id, $current_term, $current_year);
if (!$club) {
    echo "ไม่พบข้อมูลชุมนุม";
    exit;
}

// ข้อมูลครูที่ปรึกษา
$dbUsers = new DatabaseUsers();
$advisor = $dbUsers->getTeacherByUsername($club['advisor_teacher']);
$advisor_name = $advisor ? $advisor['Teach_name'] : $club['advisor_teacher'];
$advisor_tel = $advisor ? $advisor['Teach_phone'] : $club['advisor_phone'];

// ข้อมูลสมาชิก
$stmt = $pdo->prepare("SELECT * FROM club_members WHERE club_id = :club_id AND term = :term AND year = :year ORDER BY created_at ASC");
$stmt->execute(['club_id' => $club_id, 'term' => $current_term, 'year' => $current_year]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

$students = [];
foreach ($members as $row) {
    $stu = $dbUsers->getStudentByUsername($row['student_id']);
    $students[] = [
        'student_id' => $row['student_id'],
        'name' => $stu ? $stu['Stu_pre'].$stu['Stu_name'].' '.$stu['Stu_sur'] : '',
        'class_name' => $stu ? ('ม.'.$stu['Stu_major'].'/'.$stu['Stu_room'] ?? '') : '',
        'Stu_major' => $stu['Stu_major'] ?? null,
        'Stu_room' => $stu['Stu_room'] ?? null,
        'Stu_no' => $stu['Stu_no'] ?? null,
    ];
}
// จัดเรียงตาม Stu_major, Stu_room, Stu_no
usort($students, function($a, $b) {
    $cmp = intval($a['Stu_major']) <=> intval($b['Stu_major']);
    if ($cmp !== 0) return $cmp;
    $cmp = intval($a['Stu_room']) <=> intval($b['Stu_room']);
    if ($cmp !== 0) return $cmp;
    return intval($a['Stu_no']) <=> intval($b['Stu_no']);
});

$page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
    . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>พิมพ์รายชื่อสมาชิกชุมนุม</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link href="https://fonts.googleapis.com/css2?family=TH+Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body, html {
            font-family: 'TH Sarabun', 'THSarabunNew', 'Sarabun', Arial, sans-serif !important;
            font-size: 16px;
        }
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }
            body {
                margin: 0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-family: 'TH Sarabun', 'THSarabunNew', 'Sarabun', Arial, sans-serif !important;
                font-size: 16px !important;
            }
            .no-print { display: none; }
            .print-border { border: 1px solid #000 !important; }
        }
        .fade-in {
            animation: fadeIn 0.7s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(16px);}
            to { opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-white min-h-screen p-0 fade-in">
    <div class="max-w-3xl mx-auto my-8 bg-white rounded-xl shadow-lg border border-blue-200 p-8 print-border">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <span class="text-4xl"><img src="../dist/img/logo-phicha.png" alt="phichai school logo" class="rounded-full w-12 h-12"></span>
                <div>
                    <div class="text-xl font-bold text-blue-700">โรงเรียนพิชัย</div>
                    <div class="text-sm text-gray-500">ระบบรับสมัครชุมนุม</div>
                </div>
            </div>
            <button onclick="window.print()" class="no-print px-5 py-2 bg-gradient-to-r from-blue-600 to-blue-400 text-white rounded-lg shadow hover:scale-105 hover:from-blue-700 hover:to-blue-500 transition-all font-semibold text-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2V9a2 2 0 012-2h16a2 2 0 012 2v7a2 2 0 01-2 2h-2m-6 0v4m0 0h4m-4 0H8"/></svg>
                พิมพ์หน้านี้
            </button>
        </div>
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="text-lg font-semibold text-blue-800 mb-1"><?= htmlspecialchars($club['club_name']) ?></div>
                <div class="text-gray-700"><span class="font-semibold">ครูที่ปรึกษา:</span> <?= htmlspecialchars($advisor_name) ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; เบอร์โทรศัพท์: <?= htmlspecialchars($advisor_tel) ?></div>
                <div class="text-gray-700"><span class="font-semibold">ภาคเรียน/ปีการศึกษา:</span> <?= htmlspecialchars($club['term']) ?> / <?= htmlspecialchars($club['year']) ?></div>
                <div class="text-gray-700"><span class="font-semibold">รายละเอียด:</span> <?= htmlspecialchars($club['description']) ?></div>
            </div>

        </div>
        <div class="mb-2 flex items-center justify-between">
            <div class="text-blue-700 font-semibold text-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-4V6a4 4 0 00-8 0v4m12 0a4 4 0 01-8 0m8 0V6a4 4 0 00-8 0v4"/></svg>
                รายชื่อสมาชิก (<?= count($students) ?> คน)
            </div>
        </div>
        <div class="overflow-x-auto rounded-lg shadow">
            <table class="min-w-full border border-blue-300 rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-400 to-blue-600 text-white">
                        <th class="border px-3 py-2 text-center font-semibold">ลำดับ</th>
                        <th class="border px-3 py-2 text-center font-semibold">เลขประจำตัว</th>
                        <th class="border px-3 py-2 text-center font-semibold">ชื่อ-สกุล</th>
                        <th class="border px-3 py-2 text-center font-semibold">ชั้น</th>
                        <th class="border px-3 py-2 text-center font-semibold">เลขที่</th>
                        <th class="border px-3 py-2 text-center font-semibold">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $i => $stu): ?>
                    <tr class="hover:bg-blue-50 transition-all">
                        <td class="border px-3 py-1 text-center"><?= $i+1 ?></td>
                        <td class="border px-3 py-1 text-center font-mono"><?= htmlspecialchars($stu['student_id']) ?></td>
                        <td class="border px-3 py-1"><?= htmlspecialchars($stu['name']) ?></td>
                        <td class="border px-3 py-1 text-center"><?= htmlspecialchars($stu['class_name']) ?></td>
                        <td class="border px-3 py-1 text-center"><?= htmlspecialchars($stu['Stu_no']) ?></td>
                        <td class="border px-3 py-1 text-center"></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="4" class="border px-3 py-2 text-center text-gray-500">ไม่มีสมาชิกในชุมนุมนี้</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-8 flex flex-col md:flex-row md:justify-between items-end gap-4">
            <div class="text-gray-600 text-sm">
                <span class="font-semibold">รวมสมาชิก:</span> <?= count($students) ?> คน
            </div>
            <div class="text-right text-xs text-gray-400">
                พิมพ์เมื่อ <?= date('d/m/Y H:i') ?>
            </div>
        </div>
    </div>
    <script>
        // Fade-in effect (for fun)
        document.body.classList.add('fade-in');
    </script>
</body>
</html>
