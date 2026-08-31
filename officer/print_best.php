<?php
session_start();
// Allow only officer and admin role to access
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['เจ้าหน้าที่', 'admin'])) {
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

$termPee = TermPee::getCurrent();
$current_term = $termPee ? $termPee->term : 1;
$current_year = (int)($termPee ? $termPee->pee : (date('Y') + 543));

$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'] ?? [];

$db = new DatabaseClub();
$pdo = $db->getPDO();
$bestModel = new BestActivity($pdo);
$dbUsers = new DatabaseUsers();

$activity = $bestModel->getById($id);
if (!$activity) { echo 'ไม่พบกิจกรรม'; exit; }

$year = isset($_GET['year']) && intval($_GET['year']) > 0 ? intval($_GET['year']) : intval($activity['year'] ?? $current_year);

$availableYears = $bestModel->getDistinctYears();
if (empty($availableYears)) $availableYears = [$current_year];
if (!in_array($current_year, $availableYears)) array_unshift($availableYears, $current_year);
if (!in_array($year, $availableYears)) array_unshift($availableYears, $year);
$availableYears = array_values(array_unique(array_map('intval', $availableYears)));
rsort($availableYears);

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

$studentsJson = json_encode($students, JSON_UNESCAPED_UNICODE);
$activityJson = json_encode($activity, JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ใบเซ็นชื่อกิจกรรม <?= htmlspecialchars($activity['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&family=TH+Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body, html {
            font-family: 'Sarabun', 'TH Sarabun', 'THSarabunNew', sans-serif !important;
            background-color: #f3f4f6;
        }
        @media print {
            body { background-color: white; }
            .no-print { display: none !important; }
            .print-area { 
                margin-left: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
        }
        .control-panel {
            position: fixed;
            left: 20px;
            top: 20px;
            width: 320px;
            max-height: 93vh;
            overflow-y: auto;
            z-index: 100;
        }
        .control-panel::-webkit-scrollbar { width: 4px; }
        .control-panel::-webkit-scrollbar-track { background: #f1f1f1; }
        .control-panel::-webkit-scrollbar-thumb { background: #ddd; border-radius: 10px; }
        
        .paper-sheet {
            background-color: white;
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 15mm;
            margin: 10px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
            box-sizing: border-box;
        }
        @media print {
            .paper-sheet {
                width: 100% !important;
                min-height: 0 !important;
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                page-break-after: always;
                break-after: page;
            }
            .paper-sheet:last-child {
                page-break-after: avoid;
                break-after: avoid;
            }
        }
        table.print-table {
            border-collapse: collapse;
            width: 100%;
        }
        table.print-table th, table.print-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            line-height: 1.25;
            word-break: break-word;
            font-size: inherit !important;
        }
        table.print-table th {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-size: inherit !important;
        }
        .btn-print-primary {
            background-color: #ea580c !important;
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%) !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 14px 0 rgba(234, 88, 12, 0.4) !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        .btn-print-primary:hover {
            background: linear-gradient(135deg, #d97706 0%, #c2410c 100%) !important;
            color: #ffffff !important;
        }
        .btn-secondary-close {
            background: #f1f5f9 !important;
            color: #334155 !important;
            border: 1px solid #cbd5e1 !important;
        }
        .btn-secondary-close:hover {
            background: #e2e8f0 !important;
            color: #0f172a !important;
        }
    </style>
</head>
<body class="p-0 m-0">

    <!-- Control Panel (Sidebar) -->
    <div class="control-panel no-print bg-white p-5 rounded-2xl shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between border-b pb-3 mb-4">
            <h3 class="font-black text-slate-800 flex items-center gap-2 text-md">
                <i class="fas fa-print text-amber-500"></i>
                พิมพ์ใบเซ็นชื่อกิจกรรม
            </h3>
        </div>

        <!-- Activity Info Box -->
        <div class="mb-4 p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs">
            <div class="text-slate-500 font-bold mb-1">กิจกรรม:</div>
            <div class="text-sm font-black text-amber-800 flex items-center gap-1.5 mb-1">
                <i class="fas fa-star text-amber-600"></i>
                <span><?= htmlspecialchars($activity['name']) ?></span>
            </div>
            <div class="text-[11px] text-slate-600">
                ระดับชั้นที่เปิดรับ: <b><?= htmlspecialchars($activity['grade_levels'] ?: 'ทุกระดับชั้น') ?></b>
            </div>
        </div>

        <!-- Filter Year -->
        <div class="mb-3">
            <label class="block text-xs font-bold text-slate-600 mb-1">ปีการศึกษา:</label>
            <select id="filter-year" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none font-semibold">
                <?php foreach ($availableYears as $y): ?>
                <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>>ปีการศึกษา <?= $y ?> <?= $y == $current_year ? '(ปัจจุบัน)' : '' ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Format Selector -->
        <div class="mb-3">
            <label class="block text-xs font-bold text-slate-600 mb-1">รูปแบบช่องเซ็นชื่อ:</label>
            <select id="sign-format" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-400 outline-none">
                <option value="dual_time" selected>ลงชื่อมา-กลับ พร้อมเวลา (4 ช่อง)</option>
                <option value="single_sign">ลงชื่อ 1 ช่อง + หมายเหตุ (2 ช่อง)</option>
                <option value="check_only">เช็คชื่อ (ช่องติ๊ก มา/ขาด/ลา)</option>
            </select>
        </div>

        <!-- Custom Report Title -->
        <div class="mb-3">
            <label class="block text-xs font-bold text-slate-600 mb-1">หัวข้อรายงาน:</label>
            <input type="text" id="custom-title" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-amber-400 outline-none" value="แบบลงทะเบียนเข้าร่วมกิจกรรม Best For Teen">
        </div>
        
        <!-- Font Size Slider -->
        <div class="mb-3">
            <label class="block text-xs font-bold text-slate-600 mb-1">ขนาดตัวอักษร: <span id="fontSizeDisplay">11px</span></label>
            <input type="range" id="fontSizeRange" min="9" max="18" value="11" class="w-full h-2 bg-amber-100 rounded-lg appearance-none cursor-pointer accent-amber-600">
        </div>

        <!-- Signature Toggle -->
        <div class="mb-4 pt-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="show-signature" checked class="w-3.5 h-3.5 rounded text-amber-600">
                <span class="text-slate-700 font-bold text-xs">แสดงช่องลงชื่อครูผู้รับผิดชอบ</span>
            </label>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-2">
            <button onclick="window.print()" class="w-full btn-print-primary font-bold py-2.5 px-4 rounded-xl shadow-lg flex items-center justify-center gap-2 text-sm transition-all active:scale-98 cursor-pointer">
                <i class="fas fa-print"></i>
                <span>พิมพ์รายงาน (Print / PDF)</span>
            </button>
            <button onclick="window.close()" class="w-full btn-secondary-close font-bold py-2 px-4 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                <i class="fas fa-arrow-left"></i>
                <span>ย้อนกลับ / ปิดหน้านี้</span>
            </button>
        </div>
    </div>

    <!-- Print Preview / Paper Sheets Container -->
    <div class="print-area ml-0 sm:ml-80 p-4 transition-all" id="print-container">
        <!-- Rendered dynamically by JavaScript -->
    </div>

    <script>
        const students = <?= $studentsJson ?>;
        const activity = <?= $activityJson ?>;
        const schoolName = <?= json_encode($global['nameschool'] ?? 'โรงเรียนพิชัย') ?>;
        let selectedYear = <?= json_encode($year) ?>;

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('filter-year').addEventListener('change', function() {
                const y = this.value;
                const url = new URL(window.location.href);
                url.searchParams.set('year', y);
                window.location.href = url.toString();
            });

            document.getElementById('sign-format').addEventListener('change', renderSheet);
            document.getElementById('show-signature').addEventListener('change', renderSheet);
            document.getElementById('custom-title').addEventListener('input', renderSheet);

            document.getElementById('fontSizeRange').addEventListener('input', function() {
                document.getElementById('fontSizeDisplay').innerText = this.value + 'px';
                document.querySelectorAll('.paper-sheet').forEach(sheet => {
                    sheet.style.fontSize = this.value + 'px';
                });
            });

            renderSheet();
        });

        function renderSheet() {
            const customTitle = document.getElementById('custom-title').value.trim();
            const signFormat = document.getElementById('sign-format').value;
            const showSig = document.getElementById('show-signature').checked;
            const fontSize = document.getElementById('fontSizeRange').value + 'px';
            const container = document.getElementById('print-container');
            container.innerHTML = '';

            const sheet = document.createElement('div');
            sheet.className = 'paper-sheet text-black';
            sheet.style.fontSize = fontSize;

            let tableHeaders = '';
            if (signFormat === 'dual_time') {
                tableHeaders = `
                    <th style="width: 6%; text-align: center; white-space: nowrap;">ลำดับ</th>
                    <th style="width: 10%; text-align: center; white-space: nowrap;">รหัสประจำตัว</th>
                    <th style="text-align: center;">ชื่อ - นามสกุล</th>
                    <th style="width: 8%; text-align: center; white-space: nowrap;">ชั้น/ห้อง</th>
                    <th style="width: 6%; text-align: center; white-space: nowrap;">เลขที่</th>
                    <th style="width: 13%; text-align: center;">ลงชื่อมา</th>
                    <th style="width: 8%; text-align: center;">เวลามา</th>
                    <th style="width: 13%; text-align: center;">ลงชื่อกลับ</th>
                    <th style="width: 8%; text-align: center;">เวลากลับ</th>
                `;
            } else if (signFormat === 'single_sign') {
                tableHeaders = `
                    <th style="width: 6%; text-align: center; white-space: nowrap;">ลำดับ</th>
                    <th style="width: 11%; text-align: center; white-space: nowrap;">รหัสประจำตัว</th>
                    <th style="text-align: center;">ชื่อ - นามสกุล</th>
                    <th style="width: 9%; text-align: center; white-space: nowrap;">ชั้น/ห้อง</th>
                    <th style="width: 6%; text-align: center; white-space: nowrap;">เลขที่</th>
                    <th style="width: 20%; text-align: center;">ลายมือชื่อนักเรียน</th>
                    <th style="width: 14%; text-align: center;">หมายเหตุ</th>
                `;
            } else {
                tableHeaders = `
                    <th style="width: 6%; text-align: center; white-space: nowrap;">ลำดับ</th>
                    <th style="width: 11%; text-align: center; white-space: nowrap;">รหัสประจำตัว</th>
                    <th style="text-align: center;">ชื่อ - นามสกุล</th>
                    <th style="width: 9%; text-align: center; white-space: nowrap;">ชั้น/ห้อง</th>
                    <th style="width: 6%; text-align: center; white-space: nowrap;">เลขที่</th>
                    <th style="width: 7%; text-align: center;">มา</th>
                    <th style="width: 7%; text-align: center;">ขาด</th>
                    <th style="width: 7%; text-align: center;">ลา</th>
                    <th style="width: 14%; text-align: center;">หมายเหตุ</th>
                `;
            }

            let rowsHtml = '';
            if (students.length === 0) {
                const colspan = (signFormat === 'dual_time' ? 9 : (signFormat === 'single_sign' ? 7 : 9));
                rowsHtml = `<tr><td colspan="${colspan}" style="text-align: center; color: #888; padding: 25px;">ไม่มีนักเรียนลงทะเบียนในกิจกรรมนี้</td></tr>`;
            } else {
                students.forEach((s, idx) => {
                    const className = (s.Stu_major ? `ม.${s.Stu_major}/${s.Stu_room || ''}` : '-');
                    rowsHtml += '<tr>';
                    rowsHtml += `<td style="text-align: center;">${idx + 1}</td>`;
                    rowsHtml += `<td style="text-align: center; font-weight: bold;">${s.student_id}</td>`;
                    rowsHtml += `<td style="font-weight: 600; white-space: nowrap;">${s.name}</td>`;
                    rowsHtml += `<td style="text-align: center;">${className}</td>`;
                    rowsHtml += `<td style="text-align: center;">${s.Stu_no || '-'}</td>`;
                    if (signFormat === 'dual_time') {
                        rowsHtml += `<td></td><td></td><td></td><td></td>`;
                    } else if (signFormat === 'single_sign') {
                        rowsHtml += `<td></td><td></td>`;
                    } else {
                        rowsHtml += `<td></td><td></td><td></td><td></td>`;
                    }
                    rowsHtml += '</tr>';
                });
            }

            let sigHtml = '';
            if (showSig) {
                sigHtml = `
                    <div style="margin-top: 15px; display: flex; justify-content: flex-end; page-break-inside: avoid;">
                        <div style="text-align: center; width: 280px; font-size: 0.95em; line-height: 1.5;">
                            <p style="margin: 0 0 4px 0;">ลงชื่อ..............................................................ครูผู้รับผิดชอบ</p>
                            <p style="margin: 0 0 4px 0;">(..............................................................)</p>
                            <p style="margin: 0;">วันที่ ..... เดือน .................... พ.ศ. .........</p>
                        </div>
                    </div>
                `;
            }

            sheet.innerHTML = `
                <!-- Header -->
                <div style="text-align: center; margin-bottom: 12px;">
                    <h2 style="font-size: 1.4em; font-weight: bold; margin: 0; line-height: 1.2;">${schoolName}</h2>
                    <h3 style="font-size: 1.15em; font-weight: bold; margin: 3px 0 0 0; line-height: 1.2;">${customTitle}</h3>
                    <p style="font-size: 0.95em; margin: 3px 0 0 0; line-height: 1.2;">
                        กิจกรรม: <b>${activity.name}</b> | ปีการศึกษา ${selectedYear} | ระดับชั้นที่เปิดรับ: ${activity.grade_levels || 'ทุกระดับชั้น'}
                    </p>
                </div>

                <!-- Stats Bar -->
                <div style="display: flex; justify-content: space-between; font-size: 0.9em; font-weight: bold; border-bottom: 1px dashed #666; padding-bottom: 4px; margin-bottom: 8px;">
                    <span>ยอดรับทั้งหมด: <b>${activity.max_members || 0}</b> ที่นั่ง</span>
                    <span>จำนวนนักเรียนในกิจกรรม: <b style="color: #047857;">${students.length}</b> คน</span>
                    <span>คงเหลือ: <b>${Math.max(0, (activity.max_members || 0) - students.length)}</b> ที่นั่ง</span>
                    <span>อัตราการเติมเต็ม: <b>${(activity.max_members > 0) ? Math.round((students.length / activity.max_members) * 100) : 0}%</b></span>
                </div>

                <!-- Table -->
                <table class="print-table">
                    <thead>
                        <tr>${tableHeaders}</tr>
                    </thead>
                    <tbody>
                        ${rowsHtml}
                    </tbody>
                </table>

                ${sigHtml}
            `;

            container.appendChild(sheet);
        }
    </script>
</body>
</html>
