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

$dbUsers = new DatabaseUsers();
$dbClub = new DatabaseClub();
$pdoClub = $dbClub->getPDO();
$bestModel = new BestActivity($pdoClub, false);

$termPee = TermPee::getCurrent();
$current_term = $termPee ? $termPee->term : 1;
$current_year = (int)($termPee ? $termPee->pee : (date('Y') + 543));

$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'] ?? [];

$reportType = $_GET['report'] ?? 'overview'; // 'overview', 'level', 'room', 'activity'
$init_activity = isset($_GET['activity_id']) ? intval($_GET['activity_id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
if ($init_activity > 0 && !isset($_GET['report'])) {
    $reportType = 'activity';
}
$req_year = isset($_GET['year']) && intval($_GET['year']) > 0 ? intval($_GET['year']) : $current_year;
$init_level = isset($_GET['level']) ? intval($_GET['level']) : 1;
if ($init_level < 1 || $init_level > 6) $init_level = 1;
$init_room = isset($_GET['room']) ? trim($_GET['room']) : '';

// 1. Fetch all distinct years
$availableYears = $bestModel->getDistinctYears();
if (empty($availableYears)) $availableYears = [$current_year];
if (!in_array($current_year, $availableYears)) array_unshift($availableYears, $current_year);
if (!in_array($req_year, $availableYears)) array_unshift($availableYears, $req_year);
$availableYears = array_values(array_unique(array_map('intval', $availableYears)));
rsort($availableYears);

// 2. Fetch all Best activities for this year with counts
$activitiesWithCounts = $bestModel->getAllWithMemberCounts($req_year);
$activitiesMap = [];
foreach ($activitiesWithCounts as $act) {
    $activitiesMap[$act['id']] = $act['name'];
}

// 3. Fetch all active students
$sql = "SELECT Stu_id, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_no 
        FROM student 
        WHERE Stu_status = '1'
        ORDER BY CAST(Stu_major AS UNSIGNED) ASC, CAST(Stu_room AS UNSIGNED) ASC, CAST(Stu_no AS UNSIGNED) ASC, Stu_id ASC";
$stmt = $dbUsers->query($sql);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Fetch all member registrations for this year
$memberStmt = $pdoClub->prepare("SELECT student_id, activity_id, created_at FROM best_members WHERE year = :year");
$memberStmt->execute(['year' => $req_year]);
$membersMap = [];
while ($row = $memberStmt->fetch(PDO::FETCH_ASSOC)) {
    $membersMap[$row['student_id']] = $row;
}

// 5. Prepare student list with registration info
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
        'activity_id' => $reg ? $reg['activity_id'] : null,
        'activity_name' => $activityName,
        'is_registered' => !empty($reg),
        'registered_at' => $reg ? $reg['created_at'] : null
    ];
}

$studentsJson = json_encode($studentsList, JSON_UNESCAPED_UNICODE);
$activitiesJson = json_encode($activitiesWithCounts, JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>พิมพ์รายงาน Best For Teen</title>
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
                ตั้งค่าพิมพ์รายงาน
            </h3>
        </div>

        <!-- Select Report Type -->
        <div class="mb-3">
            <label class="block text-xs font-bold text-slate-600 mb-1">ประเภทรายงาน:</label>
            <select id="report-type" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none font-bold bg-amber-50/50">
                <option value="overview" <?= $reportType === 'overview' ? 'selected' : '' ?>>1. สรุปภาพรวมกิจกรรมทั้งหมด</option>
                <option value="level" <?= $reportType === 'level' ? 'selected' : '' ?>>2. สรุปสถิติตามระดับชั้น (ม.1-6)</option>
                <option value="room" <?= $reportType === 'room' ? 'selected' : '' ?>>3. รายชื่อนักเรียนรายห้อง (ม.X/Y)</option>
                <option value="activity" <?= $reportType === 'activity' ? 'selected' : '' ?>>4. ใบเซ็นชื่อตามกิจกรรม (Activity Sign Sheet)</option>
            </select>
        </div>

        <!-- Filter Year -->
        <div class="mb-3">
            <label class="block text-xs font-bold text-slate-600 mb-1">ปีการศึกษา:</label>
            <select id="filter-year" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none font-semibold">
                <?php foreach ($availableYears as $y): ?>
                <option value="<?= $y ?>" <?= $y == $req_year ? 'selected' : '' ?>>ปีการศึกษา <?= $y ?> <?= $y == $current_year ? '(ปัจจุบัน)' : '' ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Activity Filter (Visible when activity report is selected) -->
        <div id="activity-filter-container" class="<?= $reportType === 'activity' ? '' : 'hidden' ?> mb-3 p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">เลือกกิจกรรม:</label>
                <select id="filter-activity" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-semibold">
                    <option value="">ทุกกิจกรรม (พิมพ์แยกหน้าตามกิจกรรม)</option>
                    <?php foreach ($activitiesWithCounts as $act): ?>
                    <option value="<?= $act['id'] ?>" <?= $act['id'] == $init_activity ? 'selected' : '' ?>>
                        <?= htmlspecialchars($act['name']) ?> (<?= $act['current_members'] ?>/<?= $act['max_members'] ?> คน)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">รูปแบบช่องเซ็นชื่อ:</label>
                <select id="activity-sign-format" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-semibold">
                    <option value="dual_time" selected>ลงชื่อมา-กลับ พร้อมเวลา (4 ช่อง: เซ็นมา/เวลามา/เซ็นกลับ/เวลากลับ)</option>
                    <option value="single_sign">ลงชื่อ 1 ช่อง + หมายเหตุ</option>
                    <option value="check_only">เช็คชื่อ (ช่องติ๊ก มา/ขาด/ลา)</option>
                </select>
            </div>
            <div class="pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="show-activity-signature" checked class="w-3.5 h-3.5 rounded text-amber-600">
                    <span class="text-slate-700 font-bold text-xs">แสดงช่องลงชื่อครูผู้รับผิดชอบด้านล่าง</span>
                </label>
            </div>
        </div>

        <!-- Room Filter (Visible when room report is selected) -->
        <div id="room-filter-container" class="<?= $reportType === 'room' ? '' : 'hidden' ?> mb-3 p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">เลือกระดับชั้น:</label>
                <select id="filter-level" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-semibold">
                    <option value="">ทุกระดับชั้น (ม.1 - ม.6)</option>
                    <?php for($i=1; $i<=6; $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $init_level ? 'selected' : '' ?>>มัธยมศึกษาปีที่ <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">เลือกห้องเรียน:</label>
                <select id="filter-room" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-semibold">
                    <option value="">ทุกห้อง</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">การขึ้นหน้าใหม่:</label>
                <select id="page-grouping" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-semibold">
                    <option value="room" selected>แยกหน้าตามห้องเรียน (ม.X/Y)</option>
                    <option value="level">แยกหน้าตามระดับชั้น (ม.X)</option>
                    <option value="continuous">แสดงรวมต่อเนื่อง (ไม่แยกหน้า)</option>
                </select>
            </div>
        </div>

        <!-- Custom Report Title -->
        <div class="mb-3">
            <label class="block text-xs font-bold text-slate-600 mb-1">หัวข้อรายงาน:</label>
            <input type="text" id="custom-title" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-amber-400 outline-none" value="รายงานสรุปกิจกรรม Best For Teen">
        </div>
        
        <!-- Font Size Slider -->
        <div class="mb-3">
            <label class="block text-xs font-bold text-slate-600 mb-1">ขนาดตัวอักษร: <span id="fontSizeDisplay">11px</span></label>
            <input type="range" id="fontSizeRange" min="9" max="18" value="11" class="w-full h-2 bg-amber-100 rounded-lg appearance-none cursor-pointer accent-amber-600">
        </div>

        <!-- Columns Toggle (For room student list) -->
        <div id="room-columns-container" class="<?= $reportType === 'room' ? '' : 'hidden' ?> mb-4">
            <label class="block text-xs font-bold text-slate-600 mb-1.5">แสดงคอลัมน์ในตาราง:</label>
            <div class="space-y-1.5 bg-slate-50 p-2.5 rounded-xl border border-slate-200 text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="col-no" class="w-3.5 h-3.5 rounded text-amber-600">
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
                    <input type="checkbox" id="col-status" class="w-3.5 h-3.5 rounded text-amber-600">
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
        const allStudents = <?= $studentsJson ?>;
        const allActivities = <?= $activitiesJson ?>;
        const schoolName = <?= json_encode($global['nameschool'] ?? 'โรงเรียนพิชัย') ?>;
        const initialLevel = <?= json_encode($init_level) ?>;
        const initialRoom = <?= json_encode($init_room) ?>;
        let selectedYear = <?= json_encode($req_year) ?>;

        document.addEventListener('DOMContentLoaded', function() {
            populateRoomDropdown();
            if (initialRoom) {
                document.getElementById('filter-room').value = initialRoom;
            }

            // Report type change
            document.getElementById('report-type').addEventListener('change', function() {
                const isRoom = (this.value === 'room');
                const isActivity = (this.value === 'activity');
                const roomContainer = document.getElementById('room-filter-container');
                const actContainer = document.getElementById('activity-filter-container');
                const colsContainer = document.getElementById('room-columns-container');
                const titleInput = document.getElementById('custom-title');

                if (isRoom) {
                    roomContainer.classList.remove('hidden');
                    actContainer.classList.add('hidden');
                    colsContainer.classList.remove('hidden');
                    titleInput.value = 'ใบรายชื่อนักเรียนลงทะเบียนกิจกรรม Best For Teen';
                } else if (isActivity) {
                    roomContainer.classList.add('hidden');
                    actContainer.classList.remove('hidden');
                    colsContainer.classList.add('hidden');
                    titleInput.value = 'แบบลงทะเบียนเข้าร่วมกิจกรรม Best For Teen';
                } else if (this.value === 'level') {
                    roomContainer.classList.add('hidden');
                    actContainer.classList.add('hidden');
                    colsContainer.classList.add('hidden');
                    titleInput.value = 'รายงานสรุปสถิติการลงทะเบียน Best For Teen ตามระดับชั้น';
                } else {
                    roomContainer.classList.add('hidden');
                    actContainer.classList.add('hidden');
                    colsContainer.classList.add('hidden');
                    titleInput.value = 'รายงานสรุปภาพรวมกิจกรรม Best For Teen';
                }
                renderReport();
            });

            // Year change
            document.getElementById('filter-year').addEventListener('change', function() {
                const y = this.value;
                const rType = document.getElementById('report-type').value;
                const url = new URL(window.location.href);
                url.searchParams.set('year', y);
                url.searchParams.set('report', rType);
                window.location.href = url.toString();
            });

            document.getElementById('filter-level').addEventListener('change', function() {
                populateRoomDropdown();
                renderReport();
            });

            document.getElementById('filter-room').addEventListener('change', renderReport);
            document.getElementById('filter-activity').addEventListener('change', renderReport);
            document.getElementById('activity-sign-format').addEventListener('change', renderReport);
            document.getElementById('show-activity-signature').addEventListener('change', renderReport);
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
                const el = document.getElementById(id);
                if (el) el.addEventListener('change', renderReport);
            });

            // Trigger initial render
            const initialType = document.getElementById('report-type').value;
            if (initialType === 'room') {
                document.getElementById('custom-title').value = 'ใบรายชื่อนักเรียนลงทะเบียนกิจกรรม Best For Teen';
            } else if (initialType === 'activity') {
                document.getElementById('custom-title').value = 'แบบลงทะเบียนเข้าร่วมกิจกรรม Best For Teen';
            } else if (initialType === 'level') {
                document.getElementById('custom-title').value = 'รายงานสรุปสถิติการลงทะเบียน Best For Teen ตามระดับชั้น';
            } else {
                document.getElementById('custom-title').value = 'รายงานสรุปภาพรวมกิจกรรม Best For Teen';
            }
            renderReport();
        });

        function populateRoomDropdown() {
            const levelVal = document.getElementById('filter-level').value;
            const roomSelect = document.getElementById('filter-room');
            const prevRoom = roomSelect.value;
            
            roomSelect.innerHTML = '<option value="">ทุกห้อง</option>';
            
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
            const rType = document.getElementById('report-type').value;
            const customTitle = document.getElementById('custom-title').value.trim();
            const fontSize = document.getElementById('fontSizeRange').value + 'px';
            const container = document.getElementById('print-container');
            container.innerHTML = '';

            if (rType === 'overview') {
                renderOverviewReport(container, customTitle, fontSize);
            } else if (rType === 'level') {
                renderLevelReport(container, customTitle, fontSize);
            } else if (rType === 'activity') {
                renderActivityReport(container, customTitle, fontSize);
            } else {
                renderRoomReport(container, customTitle, fontSize);
            }
        }

        // 1. Overview Summary Report
        function renderOverviewReport(container, customTitle, fontSize) {
            const totalActivities = allActivities.length;
            const totalCapacity = allActivities.reduce((acc, a) => acc + parseInt(a.max_members || 0), 0);
            const totalRegistered = allActivities.reduce((acc, a) => acc + parseInt(a.current_members || 0), 0);
            const totalAvailable = Math.max(0, totalCapacity - totalRegistered);
            const overallRate = totalCapacity > 0 ? Math.round((totalRegistered / totalCapacity) * 100) : 0;

            const sheet = document.createElement('div');
            sheet.className = 'paper-sheet text-black';
            sheet.style.fontSize = fontSize;

            let rowsHtml = '';
            allActivities.forEach((a, idx) => {
                const cap = parseInt(a.max_members || 0);
                const reg = parseInt(a.current_members || 0);
                const left = Math.max(0, cap - reg);
                const pct = cap > 0 ? Math.round((reg / cap) * 100) : 0;
                const isFull = (reg >= cap && cap > 0);

                rowsHtml += `
                    <tr>
                        <td style="text-align: center;">${idx + 1}</td>
                        <td style="font-weight: 600;">${a.name}</td>
                        <td style="text-align: center;">${a.grade_levels || 'ทุกระดับชั้น'}</td>
                        <td style="text-align: center; font-weight: bold;">${cap}</td>
                        <td style="text-align: center; font-weight: bold; color: #047857;">${reg}</td>
                        <td style="text-align: center; color: ${left === 0 ? '#b91c1c' : '#000'}; font-weight: ${left === 0 ? 'bold' : 'normal'};">${left}</td>
                        <td style="text-align: center; font-weight: bold;">${pct}%</td>
                        <td style="text-align: center; font-weight: bold; color: ${isFull ? '#b91c1c' : '#047857'};">${isFull ? 'เต็มแล้ว' : 'ว่าง'}</td>
                    </tr>
                `;
            });

            sheet.innerHTML = `
                <!-- Header -->
                <div style="text-align: center; margin-bottom: 15px;">
                    <h2 style="font-size: 1.4em; font-weight: bold; margin: 0; line-height: 1.2;">${schoolName}</h2>
                    <h3 style="font-size: 1.15em; font-weight: bold; margin: 3px 0 0 0; line-height: 1.2;">${customTitle}</h3>
                    <p style="font-size: 0.95em; margin: 3px 0 0 0; line-height: 1.2;">
                        ปีการศึกษา ${selectedYear}
                    </p>
                </div>

                <!-- Summary Stats Bar -->
                <div style="display: flex; justify-content: space-between; font-size: 0.9em; font-weight: bold; border-bottom: 1px dashed #666; padding-bottom: 4px; margin-bottom: 10px;">
                    <span>กิจกรรมทั้งหมด: <b>${totalActivities}</b> กิจกรรม</span>
                    <span>ยอดรับได้ทั้งหมด: <b>${totalCapacity}</b> ที่นั่ง</span>
                    <span>ลงทะเบียนแล้ว: <b style="color: #047857;">${totalRegistered}</b> คน</span>
                    <span>คงเหลือ: <b style="color: ${totalAvailable === 0 ? '#b91c1c' : '#000'};">${totalAvailable}</b> ที่นั่ง</span>
                    <span>อัตราการเติมเต็ม: <b>${overallRate}%</b></span>
                </div>

                <!-- Table -->
                <table class="print-table">
                    <thead>
                        <tr>
                            <th style="width: 5%; text-align: center;">#</th>
                            <th style="text-align: left;">ชื่อกิจกรรม Best For Teen</th>
                            <th style="width: 14%; text-align: center;">ระดับชั้นที่เปิดรับ</th>
                            <th style="width: 9%; text-align: center;">รับได้</th>
                            <th style="width: 9%; text-align: center;">ลงแล้ว</th>
                            <th style="width: 9%; text-align: center;">คงเหลือ</th>
                            <th style="width: 9%; text-align: center;">ร้อยละ</th>
                            <th style="width: 11%; text-align: center;">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rowsHtml}
                        <tr style="background-color: #f8fafc; font-weight: bold;">
                            <td colspan="3" style="text-align: center;">รวมทั้งหมด</td>
                            <td style="text-align: center;">${totalCapacity}</td>
                            <td style="text-align: center; color: #047857;">${totalRegistered}</td>
                            <td style="text-align: center;">${totalAvailable}</td>
                            <td style="text-align: center;">${overallRate}%</td>
                            <td style="text-align: center;">-</td>
                        </tr>
                    </tbody>
                </table>
            `;

            container.appendChild(sheet);
        }

        // 2. Grade Level Summary Report
        function renderLevelReport(container, customTitle, fontSize) {
            const totalStudents = allStudents.length;
            const totalRegistered = allStudents.filter(s => s.is_registered).length;
            const totalUnregistered = totalStudents - totalRegistered;
            const overallRate = totalStudents > 0 ? Math.round((totalRegistered / totalStudents) * 100) : 0;

            const sheet = document.createElement('div');
            sheet.className = 'paper-sheet text-black';
            sheet.style.fontSize = fontSize;

            let rowsHtml = '';
            for (let lvl = 1; lvl <= 6; lvl++) {
                const lvlStudents = allStudents.filter(s => s.level == lvl);
                const countTotal = lvlStudents.length;
                const countReg = lvlStudents.filter(s => s.is_registered).length;
                const countUnreg = countTotal - countReg;
                const pct = countTotal > 0 ? Math.round((countReg / countTotal) * 100) : 0;

                rowsHtml += `
                    <tr>
                        <td style="text-align: center; font-weight: bold;">มัธยมศึกษาปีที่ ${lvl} (ม.${lvl})</td>
                        <td style="text-align: center; font-weight: bold;">${countTotal}</td>
                        <td style="text-align: center; font-weight: bold; color: #047857;">${countReg}</td>
                        <td style="text-align: center; font-weight: bold; color: #b91c1c;">${countUnreg}</td>
                        <td style="text-align: center; font-weight: bold;">${pct}%</td>
                    </tr>
                `;
            }

            sheet.innerHTML = `
                <!-- Header -->
                <div style="text-align: center; margin-bottom: 15px;">
                    <h2 style="font-size: 1.4em; font-weight: bold; margin: 0; line-height: 1.2;">${schoolName}</h2>
                    <h3 style="font-size: 1.15em; font-weight: bold; margin: 3px 0 0 0; line-height: 1.2;">${customTitle}</h3>
                    <p style="font-size: 0.95em; margin: 3px 0 0 0; line-height: 1.2;">
                        ปีการศึกษา ${selectedYear}
                    </p>
                </div>

                <!-- Summary Stats Bar -->
                <div style="display: flex; justify-content: space-between; font-size: 0.9em; font-weight: bold; border-bottom: 1px dashed #666; padding-bottom: 4px; margin-bottom: 10px;">
                    <span>นักเรียนทั้งหมด: <b>${totalStudents}</b> คน</span>
                    <span>สมัครกิจกรรมแล้ว: <b style="color: #047857;">${totalRegistered}</b> คน</span>
                    <span>ยังไม่ได้สมัคร: <b style="color: #b91c1c;">${totalUnregistered}</b> คน</span>
                    <span>ความคืบหน้ารวม: <b>${overallRate}%</b></span>
                </div>

                <!-- Table -->
                <table class="print-table">
                    <thead>
                        <tr>
                            <th style="text-align: center; width: 35%;">ระดับชั้น</th>
                            <th style="width: 16%; text-align: center;">นักเรียนทั้งหมด (คน)</th>
                            <th style="width: 16%; text-align: center;">สมัครแล้ว (คน)</th>
                            <th style="width: 16%; text-align: center;">ยังไม่สมัคร (คน)</th>
                            <th style="width: 17%; text-align: center;">ร้อยละการสมัคร (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rowsHtml}
                        <tr style="background-color: #f8fafc; font-weight: bold;">
                            <td style="text-align: center;">รวมทุกระดับชั้น</td>
                            <td style="text-align: center;">${totalStudents}</td>
                            <td style="text-align: center; color: #047857;">${totalRegistered}</td>
                            <td style="text-align: center; color: #b91c1c;">${totalUnregistered}</td>
                            <td style="text-align: center;">${overallRate}%</td>
                        </tr>
                    </tbody>
                </table>
            `;

            container.appendChild(sheet);
        }

        // 3. Room Student List Report
        function renderRoomReport(container, customTitle, fontSize) {
            const levelVal = document.getElementById('filter-level').value;
            const roomVal = document.getElementById('filter-room').value;
            const grouping = document.getElementById('page-grouping').value;

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

            // Group students
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
                if (colId) tableHeaders += '<th style="width: 14%; text-align: center;">รหัสประจำตัว</th>';
                if (colNum) tableHeaders += '<th style="width: 7%; text-align: center;">เลขที่</th>';
                tableHeaders += '<th style="text-align: left;">ชื่อ - นามสกุล</th>';
                if (colClass) tableHeaders += '<th style="width: 10%; text-align: center;">ชั้น/ห้อง</th>';
                if (colAct) tableHeaders += '<th style="width: 30%; text-align: left;">กิจกรรม Best For Teen</th>';
                if (colStatus) tableHeaders += '<th style="width: 12%; text-align: center;">สถานะ</th>';
                if (colSign) tableHeaders += '<th style="width: 16%; text-align: center;">ช่องลงชื่อ / หมายเหตุ</th>';

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
                `;

                container.appendChild(sheet);
            });
        }

        // 4. Activity Member Sign Sheet Report
        function renderActivityReport(container, customTitle, fontSize) {
            const actVal = document.getElementById('filter-activity').value;
            const signFormat = document.getElementById('activity-sign-format').value;
            const showSig = document.getElementById('show-activity-signature').checked;

            let targetActivities = allActivities;
            if (actVal) {
                targetActivities = allActivities.filter(a => a.id == actVal);
            }

            if (targetActivities.length === 0) {
                container.innerHTML = `
                    <div class="paper-sheet flex items-center justify-center text-gray-400 font-bold">
                        <div class="text-center py-20">
                            <i class="fas fa-folder-open text-4xl mb-3 block"></i>
                            ไม่พบกิจกรรมในปีการศึกษานี้
                        </div>
                    </div>`;
                return;
            }

            targetActivities.forEach(act => {
                const actMembers = allStudents.filter(s => s.activity_id == act.id);
                // Sort members by level, room, number
                actMembers.sort((a, b) => {
                    if (a.level !== b.level) return a.level - b.level;
                    if (a.room !== b.room) return a.room - b.room;
                    return (parseInt(a.number) || 0) - (parseInt(b.number) || 0);
                });

                const sheet = document.createElement('div');
                sheet.className = 'paper-sheet text-black';
                sheet.style.fontSize = fontSize;

                let tableHeaders = '';
                if (signFormat === 'dual_time') {
                    tableHeaders = `
                        <th style="width: 5%; text-align: center;">ลำดับ</th>
                        <th style="width: 14%; text-align: center;">รหัสประจำตัว</th>
                        <th style="text-align: left;">ชื่อ - นามสกุล</th>
                        <th style="width: 9%; text-align: center;">ชั้น/ห้อง</th>
                        <th style="width: 6%; text-align: center;">เลขที่</th>
                        <th style="width: 14%; text-align: center;">ลงชื่อมา</th>
                        <th style="width: 9%; text-align: center;">เวลามา</th>
                        <th style="width: 14%; text-align: center;">ลงชื่อกลับ</th>
                        <th style="width: 9%; text-align: center;">เวลากลับ</th>
                    `;
                } else if (signFormat === 'single_sign') {
                    tableHeaders = `
                        <th style="width: 5%; text-align: center;">ลำดับ</th>
                        <th style="width: 15%; text-align: center;">รหัสประจำตัว</th>
                        <th style="text-align: left;">ชื่อ - นามสกุล</th>
                        <th style="width: 10%; text-align: center;">ชั้น/ห้อง</th>
                        <th style="width: 7%; text-align: center;">เลขที่</th>
                        <th style="width: 22%; text-align: center;">ลายมือชื่อนักเรียน</th>
                        <th style="width: 18%; text-align: center;">หมายเหตุ</th>
                    `;
                } else {
                    tableHeaders = `
                        <th style="width: 5%; text-align: center;">ลำดับ</th>
                        <th style="width: 15%; text-align: center;">รหัสประจำตัว</th>
                        <th style="text-align: left;">ชื่อ - นามสกุล</th>
                        <th style="width: 10%; text-align: center;">ชั้น/ห้อง</th>
                        <th style="width: 7%; text-align: center;">เลขที่</th>
                        <th style="width: 8%; text-align: center;">มา</th>
                        <th style="width: 8%; text-align: center;">ขาด</th>
                        <th style="width: 8%; text-align: center;">ลา</th>
                        <th style="width: 16%; text-align: center;">หมายเหตุ</th>
                    `;
                }

                let rowsHtml = '';
                if (actMembers.length === 0) {
                    const colspan = (signFormat === 'dual_time' ? 9 : (signFormat === 'single_sign' ? 7 : 9));
                    rowsHtml = `<tr><td colspan="${colspan}" style="text-align: center; color: #888; padding: 20px;">ไม่มีนักเรียนลงทะเบียนในกิจกรรมนี้</td></tr>`;
                } else {
                    actMembers.forEach((s, idx) => {
                        rowsHtml += '<tr>';
                        rowsHtml += `<td style="text-align: center;">${idx + 1}</td>`;
                        rowsHtml += `<td style="text-align: center; font-weight: bold;">${s.student_id}</td>`;
                        rowsHtml += `<td style="font-weight: 600;">${s.fullname}</td>`;
                        rowsHtml += `<td style="text-align: center;">ม.${s.level}/${s.room}</td>`;
                        rowsHtml += `<td style="text-align: center;">${s.number || '-'}</td>`;
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
                        <div style="margin-top: 25px; display: flex; justify-content: flex-end; page-break-inside: avoid;">
                            <div style="text-align: center; width: 280px; font-size: 0.95em;">
                                <p style="margin-bottom: 40px;">ลงชื่อ..............................................................ครูผู้รับผิดชอบ</p>
                                <p>(..............................................................)</p>
                                <p style="margin-top: 4px;">วันที่ ..... เดือน .................... พ.ศ. .........</p>
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
                            กิจกรรม: <b>${act.name}</b> | ปีการศึกษา ${selectedYear} | ระดับชั้นที่เปิดรับ: ${act.grade_levels || 'ทุกระดับชั้น'}
                        </p>
                    </div>

                    <!-- Stats Bar -->
                    <div style="display: flex; justify-content: space-between; font-size: 0.9em; font-weight: bold; border-bottom: 1px dashed #666; padding-bottom: 4px; margin-bottom: 8px;">
                        <span>ยอดรับทั้งหมด: <b>${act.max_members || 0}</b> ที่นั่ง</span>
                        <span>จำนวนนักเรียนในกิจกรรม: <b style="color: #047857;">${actMembers.length}</b> คน</span>
                        <span>คงเหลือ: <b>${Math.max(0, (act.max_members || 0) - actMembers.length)}</b> ที่นั่ง</span>
                        <span>อัตราการเติมเต็ม: <b>${(act.max_members > 0) ? Math.round((actMembers.length / act.max_members) * 100) : 0}%</b></span>
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
            });
        }
    </script>
</body>
</html>
