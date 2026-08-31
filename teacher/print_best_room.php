<?php
session_start();
// Allow teacher and officer roles
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ครู', 'เจ้าหน้าที่', 'admin'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../classes/DatabaseClub.php';
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../models/BestActivity.php';
require_once __DIR__ . '/../models/TermPee.php';

use App\DatabaseClub;
use App\DatabaseUsers;

$dbUsers = new DatabaseUsers();
$dbClub = new DatabaseClub();
$pdoClub = $dbClub->getPDO();
$bestModel = new BestActivity($pdoClub, true);

$termPee = TermPee::getCurrent();
$current_term = $termPee ? $termPee->term : 1;
$current_year = (int)($termPee ? $termPee->pee : (date('Y') + 543));

$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

$level = $_GET['level'] ?? '1';
$room = $_GET['room'] ?? '1';
$req_year = isset($_GET['year']) && intval($_GET['year']) > 0 ? intval($_GET['year']) : $current_year;

// Normalize level
if (preg_match('/ม\.(\d+)/u', $level, $m)) {
    $init_level = (int)$m[1];
} else {
    $init_level = (int)$level;
}
if ($init_level < 1 || $init_level > 6) $init_level = 1;
$init_room = trim($room);

// Fetch all active students
$sql = "SELECT Stu_id, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_no 
        FROM student 
        WHERE Stu_status = '1'
        ORDER BY CAST(Stu_major AS UNSIGNED) ASC, CAST(Stu_room AS UNSIGNED) ASC, CAST(Stu_no AS UNSIGNED) ASC, Stu_id ASC";
$stmt = $dbUsers->query($sql);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all Best For Teen activities for lookup in the requested year
$activitiesMap = [];
$actStmt = $pdoClub->prepare("SELECT id, name, description FROM best_activities WHERE year = :year");
$actStmt->execute(['year' => $req_year]);
while ($r = $actStmt->fetch(PDO::FETCH_ASSOC)) {
    $activitiesMap[$r['id']] = $r['name'];
}

// Fetch member registrations
$memberStmt = $pdoClub->prepare("SELECT student_id, activity_id, created_at FROM best_members WHERE year = :year");
$memberStmt->execute(['year' => $req_year]);
$membersMap = [];
while ($row = $memberStmt->fetch(PDO::FETCH_ASSOC)) {
    $membersMap[$row['student_id']] = $row;
}

// Prepare student list for JS
$studentsList = [];
foreach ($students as $stu) {
    $sid = $stu['Stu_id'];
    $fullname = $stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur'];
    $reg = $membersMap[$sid] ?? null;
    $activityName = '-';
    if ($reg && isset($activitiesMap[$reg['activity_id']])) {
        $activityName = $activitiesMap[$reg['activity_id']];
    } elseif ($reg) {
        $activityName = 'กิจกรรม #' . $reg['activity_id'];
    }

    $studentsList[] = [
        'student_id' => $sid,
        'fullname' => $fullname,
        'level' => intval($stu['Stu_major']),
        'room' => intval($stu['Stu_room']),
        'number' => $stu['Stu_no'] !== null ? intval($stu['Stu_no']) : '',
        'activity_name' => $activityName,
        'is_registered' => !empty($reg),
        'registered_at' => $reg ? $reg['created_at'] : null
    ];
}

$studentsJson = json_encode($studentsList, JSON_UNESCAPED_UNICODE);
$availableYears = $bestModel->getAvailableYears();
if (empty($availableYears)) $availableYears = [$current_year];
if (!in_array($current_year, $availableYears)) array_unshift($availableYears, $current_year);
rsort($availableYears);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>พิมพ์รายชื่อนักเรียน Best For Teen</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
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
            .print-border { border: 1px solid #000 !important; }
        }
        table.print-table {
            border-collapse: collapse;
            width: 100%;
        }
        table.print-table th, table.print-table td {
            border: 1px solid #000;
            padding: 4px 6px;
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
    </style>
</head>
<body class="p-0 m-0">

    <!-- Control Panel (Sidebar) -->
    <div class="control-panel no-print bg-white p-5 rounded-2xl shadow-2xl border border-slate-200">
        <h3 class="font-black text-slate-800 mb-4 flex items-center gap-2 text-md border-b pb-2">
            <i class="fas fa-cog text-amber-500"></i>
            ตั้งค่ารายงาน Best For Teen
        </h3>

        <!-- Filter Year -->
        <div class="mb-3">
            <label class="block text-xs font-bold text-slate-600 mb-1">ปีการศึกษา:</label>
            <select id="filter-year" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none font-semibold">
                <?php foreach ($availableYears as $y): ?>
                <option value="<?= $y ?>" <?= $y == $req_year ? 'selected' : '' ?>>ปีการศึกษา <?= $y ?> <?= $y == $current_year ? '(ปัจจุบัน)' : '' ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Filter Grade Level -->
        <div class="mb-3">
            <label class="block text-xs font-bold text-slate-600 mb-1">เลือกระดับชั้น:</label>
            <select id="filter-level" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none font-semibold">
                <option value="">ทั้งหมด (ทุกระดับชั้น)</option>
                <option value="1">มัธยมศึกษาปีที่ 1 (ม.1)</option>
                <option value="2">มัธยมศึกษาปีที่ 2 (ม.2)</option>
                <option value="3">มัธยมศึกษาปีที่ 3 (ม.3)</option>
                <option value="4">มัธยมศึกษาปีที่ 4 (ม.4)</option>
                <option value="5">มัธยมศึกษาปีที่ 5 (ม.5)</option>
                <option value="6">มัธยมศึกษาปีที่ 6 (ม.6)</option>
            </select>
        </div>

        <!-- Filter Room -->
        <div class="mb-3">
            <label class="block text-xs font-bold text-slate-600 mb-1">เลือกห้องเรียน:</label>
            <select id="filter-room" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none font-semibold">
                <option value="">ทั้งหมด (ทุกห้อง)</option>
            </select>
        </div>
        
        <!-- Page Grouping Options -->
        <div class="mb-3 border-t pt-2">
            <label class="block text-xs font-bold text-slate-600 mb-1">รูปแบบการจัดกลุ่ม / ขึ้นหน้าใหม่:</label>
            <select id="page-grouping" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none font-semibold">
                <option value="room" selected>แยกหน้าตามห้องเรียน (ม.X/Y)</option>
                <option value="level">แยกหน้าตามระดับชั้น (ม.X)</option>
                <option value="continuous">แสดงรวมกันต่อเนื่อง (ไม่แยกหน้า)</option>
            </select>
        </div>

        <!-- Custom Report Title -->
        <div class="mb-3">
            <label class="block text-xs font-bold text-slate-600 mb-1">หัวข้อรายงานหลัก:</label>
            <input type="text" id="custom-title" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none" value="ใบรายชื่อนักเรียนลงทะเบียนกิจกรรม Best For Teen">
        </div>
        
        <!-- Font Size Slider -->
        <div class="mb-3">
            <label class="block text-xs font-bold text-slate-600 mb-1">ขนาดตัวอักษรข้อมูล: <span id="fontSizeDisplay">11px</span></label>
            <input type="range" id="fontSizeRange" min="9" max="18" value="11" class="w-full h-2 bg-amber-100 rounded-lg appearance-none cursor-pointer accent-amber-600">
        </div>

        <!-- Columns Toggle checkboxes -->
        <div class="mb-4">
            <label class="block text-xs font-bold text-slate-600 mb-1.5">แสดงคอลัมน์ในตาราง:</label>
            <div class="space-y-1.5 bg-slate-50 p-2.5 rounded-xl border border-slate-200 text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="col-no" checked class="w-3.5 h-3.5 rounded text-amber-600">
                    <span class="text-slate-700 font-medium">ลำดับที่</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="col-id" checked class="w-3.5 h-3.5 rounded text-amber-600">
                    <span class="text-slate-700 font-medium">รหัสประจำตัว</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="col-num" checked class="w-3.5 h-3.5 rounded text-amber-600">
                    <span class="text-slate-700 font-medium">เลขที่</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="col-name" checked disabled class="w-3.5 h-3.5 rounded text-amber-600 opacity-60">
                    <span class="text-slate-700 font-medium">ชื่อ - นามสกุล (บังคับ)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="col-class" checked class="w-3.5 h-3.5 rounded text-amber-600">
                    <span class="text-slate-700 font-medium">ชั้น/ห้อง</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="col-activity" checked class="w-3.5 h-3.5 rounded text-amber-600">
                    <span class="text-slate-700 font-medium">กิจกรรม Best For Teen</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="col-status" checked class="w-3.5 h-3.5 rounded text-amber-600">
                    <span class="text-slate-700 font-medium">สถานะการสมัคร</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="col-sign" checked class="w-3.5 h-3.5 rounded text-amber-600">
                    <span class="text-slate-700 font-medium">ช่องลงชื่อ / หมายเหตุ</span>
                </label>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-2">
            <button onclick="window.print()" class="w-full bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-lg flex items-center justify-center gap-2 text-sm transition-all active:scale-98">
                <i class="fas fa-print"></i>
                <span>พิมพ์เอกสาร (Print / PDF)</span>
            </button>
            <button onclick="window.close()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2 px-4 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5">
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
        const allStudents = <?= $studentsJson ?>;
        const schoolName = <?= json_encode($global['nameschool'] ?? 'โรงเรียนพิชัย') ?>;
        const initialLevel = <?= json_encode($init_level) ?>;
        const initialRoom = <?= json_encode($init_room) ?>;
        let selectedYear = <?= json_encode($req_year) ?>;

        document.addEventListener('DOMContentLoaded', function() {
            // Set initial filter values
            if (initialLevel) {
                document.getElementById('filter-level').value = initialLevel;
            }
            populateRoomDropdown();
            if (initialRoom) {
                document.getElementById('filter-room').value = initialRoom;
            }

            // Bind event listeners
            document.getElementById('filter-year').addEventListener('change', function() {
                const y = this.value;
                const url = new URL(window.location.href);
                url.searchParams.set('year', y);
                window.location.href = url.toString();
            });

            document.getElementById('filter-level').addEventListener('change', function() {
                populateRoomDropdown();
                renderReport();
            });

            document.getElementById('filter-room').addEventListener('change', renderReport);
            document.getElementById('page-grouping').addEventListener('change', renderReport);
            document.getElementById('custom-title').addEventListener('input', renderReport);

            // Font size slider
            document.getElementById('fontSizeRange').addEventListener('input', function() {
                document.getElementById('fontSizeDisplay').innerText = this.value + 'px';
                document.querySelectorAll('.paper-sheet').forEach(sheet => {
                    sheet.style.fontSize = this.value + 'px';
                });
            });

            // Checkbox changes
            ['col-no', 'col-id', 'col-num', 'col-class', 'col-activity', 'col-status', 'col-sign'].forEach(id => {
                document.getElementById(id).addEventListener('change', renderReport);
            });

            // Initial render
            renderReport();
        });

        function populateRoomDropdown() {
            const levelVal = document.getElementById('filter-level').value;
            const roomSelect = document.getElementById('filter-room');
            const prevRoom = roomSelect.value;
            
            roomSelect.innerHTML = '<option value="">ทั้งหมด (ทุกห้อง)</option>';
            
            let filtered = allStudents;
            if (levelVal) {
                filtered = filtered.filter(s => s.level == levelVal);
            }
            
            const rooms = [...new Set(filtered.map(s => s.room))].sort((a,b) => a - b);
            rooms.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r;
                opt.textContent = `ห้อง ${r}`;
                if (r == prevRoom) opt.selected = true;
                roomSelect.appendChild(opt);
            });
        }

        function renderReport() {
            const levelVal = document.getElementById('filter-level').value;
            const roomVal = document.getElementById('filter-room').value;
            const grouping = document.getElementById('page-grouping').value;
            const customTitle = document.getElementById('custom-title').value.trim() || 'ใบรายชื่อนักเรียนลงทะเบียนกิจกรรม Best For Teen';
            const fontSize = document.getElementById('fontSizeRange').value + 'px';

            const colNo = document.getElementById('col-no').checked;
            const colId = document.getElementById('col-id').checked;
            const colNum = document.getElementById('col-num').checked;
            const colClass = document.getElementById('col-class').checked;
            const colAct = document.getElementById('col-activity').checked;
            const colStatus = document.getElementById('col-status').checked;
            const colSign = document.getElementById('col-sign').checked;

            let filtered = allStudents;
            if (levelVal) {
                filtered = filtered.filter(s => s.level == levelVal);
            }
            if (roomVal) {
                filtered = filtered.filter(s => s.room == roomVal);
            }

            const container = document.getElementById('print-container');
            container.innerHTML = '';

            if (filtered.length === 0) {
                container.innerHTML = `
                    <div class="paper-sheet flex items-center justify-center text-gray-400 font-bold">
                        <div class="text-center py-20">
                            <i class="fas fa-folder-open text-4xl mb-3 block"></i>
                            ไม่พบข้อมูลนักเรียนตามเงื่อนไขที่เลือก
                        </div>
                    </div>`;
                return;
            }

            // Group students based on selected grouping
            let groups = {};
            if (grouping === 'room') {
                filtered.forEach(s => {
                    const key = `ม.${s.level}/${s.room}`;
                    if (!groups[key]) groups[key] = [];
                    groups[key].push(s);
                });
            } else if (grouping === 'level') {
                filtered.forEach(s => {
                    const key = `ชั้นมัธยมศึกษาปีที่ ${s.level} (ม.${s.level})`;
                    if (!groups[key]) groups[key] = [];
                    groups[key].push(s);
                });
            } else {
                groups['นักเรียนทั้งหมด'] = filtered;
            }

            // Render each group as A4 sheet(s)
            Object.keys(groups).forEach(groupTitle => {
                const studentsInGroup = groups[groupTitle];
                const totalStudents = studentsInGroup.length;
                const regCount = studentsInGroup.filter(s => s.is_registered).length;
                const unregCount = totalStudents - regCount;

                const sheet = document.createElement('div');
                sheet.className = 'paper-sheet text-black';
                sheet.style.fontSize = fontSize;

                let tableHeaders = '';
                if (colNo) tableHeaders += '<th style="width: 5%; text-align: center;">#</th>';
                if (colId) tableHeaders += '<th style="width: 12%; text-align: center;">รหัสประจำตัว</th>';
                if (colNum) tableHeaders += '<th style="width: 6%; text-align: center;">เลขที่</th>';
                tableHeaders += '<th style="text-align: left;">ชื่อ - นามสกุล</th>';
                if (colClass) tableHeaders += '<th style="width: 10%; text-align: center;">ชั้น/ห้อง</th>';
                if (colAct) tableHeaders += '<th style="width: 32%; text-align: left;">กิจกรรม Best For Teen</th>';
                if (colStatus) tableHeaders += '<th style="width: 12%; text-align: center;">สถานะ</th>';
                if (colSign) tableHeaders += '<th style="width: 14%; text-align: center;">ลงชื่อ / หมายเหตุ</th>';

                let rowsHtml = '';
                studentsInGroup.forEach((s, idx) => {
                    const isReg = s.is_registered;
                    rowsHtml += '<tr>';
                    if (colNo) rowsHtml += `<td style="text-align: center;">${idx + 1}</td>`;
                    if (colId) rowsHtml += `<td style="text-align: center; font-weight: bold;">${s.student_id}</td>`;
                    if (colNum) rowsHtml += `<td style="text-align: center;">${s.number || '-'}</td>`;
                    rowsHtml += `<td style="font-weight: 600;">${s.fullname}</td>`;
                    if (colClass) rowsHtml += `<td style="text-align: center;">ม.${s.level}/${s.room}</td>`;
                    if (colAct) rowsHtml += `<td style="font-weight: ${isReg ? 'bold' : 'normal'}; color: ${isReg ? '#000' : '#888'};">${s.activity_name}</td>`;
                    if (colStatus) rowsHtml += `<td style="text-align: center; font-weight: bold; color: ${isReg ? '#047857' : '#b91c1c'};">${isReg ? 'สมัครแล้ว' : 'ยังไม่สมัคร'}</td>`;
                    if (colSign) rowsHtml += `<td style="text-align: center;"></td>`;
                    rowsHtml += '</tr>';
                });

                sheet.innerHTML = `
                    <!-- Header -->
                    <div style="text-align: center; margin-bottom: 12px;">
                        <h2 style="font-size: 1.4em; font-weight: bold; margin: 0; line-height: 1.2;">${schoolName}</h2>
                        <h3 style="font-size: 1.15em; font-weight: bold; margin: 3px 0 0 0; line-height: 1.2;">${customTitle}</h3>
                        <p style="font-size: 0.95em; margin: 3px 0 0 0; line-height: 1.2;">
                            ${groupTitle} | ปีการศึกษา ${selectedYear}
                        </p>
                    </div>

                    <!-- Summary Stats Bar -->
                    <div style="display: flex; justify-content: space-between; font-size: 0.9em; font-weight: bold; border-bottom: 1px dashed #666; padding-bottom: 4px; margin-bottom: 8px;">
                        <span>จำนวนนักเรียนทั้งหมด: <b>${totalStudents}</b> คน</span>
                        <span>สมัครกิจกรรมแล้ว: <b style="color: #047857;">${regCount}</b> คน</span>
                        <span>ยังไม่ได้สมัคร: <b style="color: #b91c1c;">${unregCount}</b> คน</span>
                        <span>ความคืบหน้า: <b>${totalStudents > 0 ? Math.round((regCount / totalStudents) * 100) : 0}%</b></span>
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

                    <!-- Signature Footer -->
                    <div style="margin-top: 25px; display: flex; justify-content: space-between; font-size: 0.9em; line-height: 1.5; page-break-inside: avoid; break-inside: avoid;">
                        <div style="text-align: center; width: 45%;">
                            <p style="margin-bottom: 35px;">ลงชื่อ............................................................ครูที่ปรึกษา</p>
                            <p>(............................................................)</p>
                            <p>วันที่ ......./......./.......</p>
                        </div>
                        <div style="text-align: center; width: 45%;">
                            <p style="margin-bottom: 35px;">ลงชื่อ............................................................หัวหน้างานกิจกรรม</p>
                            <p>(............................................................)</p>
                            <p>วันที่ ......./......./.......</p>
                        </div>
                    </div>
                `;

                container.appendChild(sheet);
            });
        }
    </script>
</body>
</html>
