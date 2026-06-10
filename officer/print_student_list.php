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

// Normalize initial filter values
$init_level = '';
if ($level) {
    if (preg_match('/ม\.(\d+)/u', $level, $m)) {
        $init_level = $m[1];
    } else {
        $init_level = $level;
    }
}
$init_room = $room;

// Query all active students to support dynamic grade/room selection in the UI
$sql = "SELECT Stu_id, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_no 
        FROM student 
        WHERE Stu_status = '1'
        ORDER BY Stu_major ASC, Stu_room ASC, Stu_no ASC";
$stmt = $dbUsers->query($sql);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch club registrations
$pdoClub = $dbClub->getPDO();
$clubStmt = $pdoClub->prepare("SELECT student_id, club_id FROM club_members WHERE term = :term AND year = :year");
$clubStmt->execute(['term' => $current_term, 'year' => $current_year]);
$clubMembers = [];
while ($row = $clubStmt->fetch(PDO::FETCH_ASSOC)) {
    $clubMembers[$row['student_id']] = $row['club_id'];
}

// Fetch all clubs for lookup
$clubsInfo = [];
$clubNameStmt = $pdoClub->query("SELECT club_id, club_name, advisor_teacher FROM clubs WHERE term = '$current_term' AND year = '$current_year'");
while ($row = $clubNameStmt->fetch(PDO::FETCH_ASSOC)) {
    $clubsInfo[$row['club_id']] = [
        'club_name' => $row['club_name'],
        'advisor_teacher' => $row['advisor_teacher']
    ];
}

$advisorNameCache = [];

// Prepare data for JS
$studentsList = [];
foreach ($students as $stu) {
    $student_id = $stu['Stu_id'];
    $fullname = $stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur'];
    
    $club_id = $clubMembers[$student_id] ?? '';
    $club_name = '-';
    $advisor_name = '-';
    
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
    }

    $studentsList[] = [
        'student_id' => $student_id,
        'fullname' => $fullname,
        'level' => intval($stu['Stu_major']),
        'room' => intval($stu['Stu_room']),
        'number' => $stu['Stu_no'] !== null ? intval($stu['Stu_no']) : '',
        'club' => $club_name,
        'advisor' => $advisor_name
    ];
}

$studentsJson = json_encode($studentsList, JSON_UNESCAPED_UNICODE);

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>พิมพ์รายชื่อสมาชิกชุมนุม</title>
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
        /* Custom scrollbar for control panel */
        .control-panel::-webkit-scrollbar {
            width: 4px;
        }
        .control-panel::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .control-panel::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 10px;
        }
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
            line-height: 1.2;
            word-break: break-word;
            font-size: inherit !important; /* Forces font-size to inherit from the A4 parent sheet */
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
            <i class="fas fa-cog text-violet-500"></i>
            ตั้งค่ารายงานและการพิมพ์
        </h3>

        <!-- Filter Grade Level -->
        <div class="mb-4">
            <label class="block text-xs font-bold text-slate-600 mb-1">เลือกระดับชั้น:</label>
            <select id="filter-level" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-400 outline-none font-semibold">
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
        <div class="mb-4">
            <label class="block text-xs font-bold text-slate-600 mb-1">เลือกห้องเรียน:</label>
            <select id="filter-room" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-400 outline-none font-semibold">
                <option value="">ทั้งหมด (ทุกห้อง)</option>
            </select>
        </div>
        
        <!-- Page Grouping Options -->
        <div class="mb-4 border-t pt-3">
            <label class="block text-xs font-bold text-slate-600 mb-1">รูปแบบการจัดกลุ่ม / ขึ้นหน้าใหม่:</label>
            <select id="page-grouping" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-400 outline-none font-semibold">
                <option value="room" <?= $type === 'room' ? 'selected' : '' ?>>แยกหน้าตามห้องเรียน (ม.X/Y)</option>
                <option value="level" <?= $type === 'level' ? 'selected' : '' ?>>แยกหน้าตามระดับชั้น (ม.X)</option>
                <option value="continuous" <?= $type === 'school' ? 'selected' : '' ?>>แสดงรวมกันต่อเนื่อง (ไม่แยกหน้า)</option>
            </select>
        </div>

        <!-- Custom Report Title -->
        <div class="mb-4">
            <label class="block text-xs font-bold text-slate-600 mb-1">หัวข้อรายงานหลัก:</label>
            <input type="text" id="custom-title" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-violet-400 outline-none" value="ใบรายชื่อนักเรียนลงทะเบียนชุมนุม">
        </div>
        
        <!-- Font Size Slider -->
        <div class="mb-4">
            <label class="block text-xs font-bold text-slate-600 mb-1">ขนาดตัวอักษรข้อมูล: <span id="fontSizeDisplay">15px</span></label>
            <input type="range" id="fontSizeRange" min="10" max="24" value="15" class="w-full h-2 bg-violet-100 rounded-lg appearance-none cursor-pointer accent-violet-600">
        </div>

        <!-- Columns Toggle checkboxes -->
        <div class="mb-4">
            <label class="block text-xs font-bold text-slate-600 mb-1.5">แสดงคอลัมน์ในตาราง:</label>
            <div class="space-y-1.5 bg-slate-50 p-3 rounded-xl border border-slate-200">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="col-id" checked class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-xs text-slate-700 font-semibold">เลขประจำตัวนักเรียน</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="col-class" checked class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-xs text-slate-700 font-semibold">ระดับชั้น/ห้อง (ม.X/Y)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="col-club" checked class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-xs text-slate-700 font-semibold">ชุมนุมที่เลือก</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="col-advisor" checked class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-xs text-slate-700 font-semibold">ครูที่ปรึกษาชุมนุม</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="col-remarks" checked class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                    <span class="text-xs text-slate-700 font-semibold">ช่องลายมือชื่อ / หมายเหตุ</span>
                </label>
            </div>
        </div>

        <!-- Signature Toggle -->
        <div class="mb-5">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="show-signature" checked class="w-4 h-4 rounded text-violet-600 focus:ring-violet-500 border-slate-300">
                <span class="text-xs text-slate-700 font-bold">แสดงช่องลงชื่อเจ้าหน้าที่ด้านล่าง</span>
            </label>
        </div>

        <!-- Print Actions -->
        <div class="space-y-2">
            <button onclick="window.print()" class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-bold py-2.5 rounded-xl shadow-lg hover:shadow-violet-200 transition-all flex items-center justify-center gap-2 text-sm">
                <i class="fas fa-print"></i> พิมพ์ใบรายชื่อ (Print)
            </button>
            <button onclick="window.close()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2 rounded-xl transition-all text-xs">
                ปิดหน้าต่าง
            </button>
        </div>
    </div>

    <!-- Paper Render Area -->
    <div class="print-area" id="renderArea" style="margin-left: 350px; padding: 20px;">
        <!-- Dynamic Sheets are injected here by JS -->
    </div>

    <script>
        // Loaded student registrations from PHP
        const students = <?= $studentsJson ?>;
        const currentTerm = <?= json_encode($current_term) ?>;
        const currentYear = <?= json_encode($current_year) ?>;

        const initLevel = <?= json_encode($init_level) ?>;
        const initRoom = <?= json_encode($init_room) ?>;
        
        // Element Bindings
        const filterLevel = document.getElementById('filter-level');
        const filterRoom = document.getElementById('filter-room');
        const groupingSelect = document.getElementById('page-grouping');
        const customTitleInput = document.getElementById('custom-title');
        const fontSizeRange = document.getElementById('fontSizeRange');
        const fontSizeDisplay = document.getElementById('fontSizeDisplay');
        const colIdCheck = document.getElementById('col-id');
        const colClassCheck = document.getElementById('col-class');
        const colClubCheck = document.getElementById('col-club');
        const colAdvisorCheck = document.getElementById('col-advisor');
        const colRemarksCheck = document.getElementById('col-remarks');
        const showSignatureCheck = document.getElementById('show-signature');
        const renderArea = document.getElementById('renderArea');

        function updateRoomOptions() {
            const lvl = filterLevel.value;
            const prevRoom = filterRoom.value;
            
            filterRoom.innerHTML = '<option value="">ทั้งหมด (ทุกห้อง)</option>';
            if (lvl) {
                let maxRoom = 12;
                if (lvl === '4' || lvl === '5' || lvl === '6') {
                    maxRoom = 7;
                }
                for (let i = 1; i <= maxRoom; i++) {
                    filterRoom.innerHTML += `<option value="${i}">ห้อง ${i}</option>`;
                }
                // restore value if it still fits
                if (parseInt(prevRoom) <= maxRoom) {
                    filterRoom.value = prevRoom;
                }
            } else {
                for (let i = 1; i <= 12; i++) {
                    filterRoom.innerHTML += `<option value="${i}">ห้อง ${i}</option>`;
                }
                filterRoom.value = prevRoom;
            }
        }

        function renderSheets() {
            // Read configuration values
            const grouping = groupingSelect.value;
            const customTitle = customTitleInput.value || 'ใบรายชื่อนักเรียนลงทะเบียนชุมนุม';
            const fSize = fontSizeRange.value;
            fontSizeDisplay.innerText = fSize + 'px';

            const selectedLevel = filterLevel.value;
            const selectedRoom = filterRoom.value;

            const showId = colIdCheck.checked;
            const showClass = colClassCheck.checked;
            const showClub = colClubCheck.checked;
            const showAdvisor = colAdvisorCheck.checked;
            const showRemarks = colRemarksCheck.checked;
            const showSignature = showSignatureCheck.checked;

            // Clear render area
            renderArea.innerHTML = '';

            // Filter students based on dropdown values
            let filteredStudents = students;
            if (selectedLevel) {
                filteredStudents = filteredStudents.filter(s => s.level === parseInt(selectedLevel));
            }
            if (selectedRoom) {
                filteredStudents = filteredStudents.filter(s => s.room === parseInt(selectedRoom));
            }

            // Group students
            let groups = {};
            if (grouping === 'room') {
                // Group by grade level (Stu_major) and room (Stu_room)
                filteredStudents.forEach(s => {
                    const key = `ชั้นมัธยมศึกษาปีที่ ${s.level}/${s.room}`;
                    if (!groups[key]) groups[key] = [];
                    groups[key].push(s);
                });
            } else if (grouping === 'level') {
                // Group by grade level only (Stu_major)
                filteredStudents.forEach(s => {
                    const key = `ชั้นมัธยมศึกษาปีที่ ${s.level}`;
                    if (!groups[key]) groups[key] = [];
                    groups[key].push(s);
                });
            } else {
                // Single continuous list
                groups['นักเรียนทั้งหมด'] = filteredStudents;
            }

            const groupKeys = Object.keys(groups);
            const totalGroups = groupKeys.length;

            if (totalGroups === 0 || filteredStudents.length === 0) {
                renderArea.innerHTML = `
                    <div class="paper-sheet flex items-center justify-center">
                        <p class="text-gray-400 font-bold text-lg">ไม่พบข้อมูลนักเรียนตามเงื่อนไขที่เลือก</p>
                    </div>
                `;
                return;
            }

            // Generate sheets
            groupKeys.forEach((groupKey, index) => {
                const groupStudents = groups[groupKey];
                const isLast = index === totalGroups - 1;

                // Statistical summaries
                const totalStudents = groupStudents.length;
                const registeredCount = groupStudents.filter(s => s.club !== '-').length;
                const unregisteredCount = totalStudents - registeredCount;

                // Create paper container
                const sheet = document.createElement('div');
                sheet.className = `paper-sheet ${!isLast ? 'page-break' : ''}`;
                sheet.style.fontSize = fSize + 'px';

                // Format print date
                const printDate = new Date().toLocaleString('th-TH', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                // Build table header
                let headerCols = `<th class="text-center font-bold" style="width: 8%">เลขที่</th>`;
                if (showId) headerCols += `<th class="text-center font-bold" style="width: 15%">เลขประจำตัว</th>`;
                headerCols += `<th class="text-left font-bold" style="width: 28%">ชื่อ-สกุล</th>`;
                if (showClass) headerCols += `<th class="text-center font-bold" style="width: 12%">ชั้น/ห้อง</th>`;
                if (showClub) headerCols += `<th class="text-left font-bold" style="width: 25%">ชุมนุมที่เลือก</th>`;
                if (showAdvisor) headerCols += `<th class="text-left font-bold" style="width: 20%">ครูที่ปรึกษา</th>`;
                if (showRemarks) headerCols += `<th class="text-center font-bold" style="width: 15%">หมายเหตุ</th>`;

                // Build table body rows
                let rowHtml = '';
                groupStudents.forEach((s, i) => {
                    rowHtml += `
                        <tr>
                            <td class="text-center font-bold">${s.number || '-'}</td>
                            ${showId ? `<td class="text-center font-mono">${s.student_id}</td>` : ''}
                            <td>${s.fullname}</td>
                            ${showClass ? `<td class="text-center">ม.${s.level}/${s.room}</td>` : ''}
                            ${showClub ? `<td class="${s.club === '-' ? 'text-red-500 font-semibold' : ''}">${s.club}</td>` : ''}
                            ${showAdvisor ? `<td>${s.advisor}</td>` : ''}
                            ${showRemarks ? `<td></td>` : ''}
                        </tr>
                    `;
                });

                // Inject content
                sheet.innerHTML = `
                    <div class="flex justify-between items-start border-b pb-4 mb-6 print-border">
                        <div class="flex items-center gap-3">
                            <img src="../dist/img/logo-phicha.png" alt="phichai school logo" class="w-12 h-12 rounded-full">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">โรงเรียนพิชัย</h2>
                                <h3 class="text-sm font-semibold text-gray-500">${customTitle}</h3>
                            </div>
                        </div>
                        <div class="text-right text-sm">
                            <div class="font-bold text-blue-700 text-lg">${groupKey}</div>
                            <div class="text-gray-500">ภาคเรียนที่ ${currentTerm} ปีการศึกษา ${currentYear}</div>
                        </div>
                    </div>

                    <table class="print-table">
                        <thead>
                            <tr>
                                ${headerCols}
                            </tr>
                        </thead>
                        <tbody>
                            ${rowHtml}
                        </tbody>
                    </table>

                    <div class="mt-6 flex flex-col md:flex-row justify-between items-start gap-4 border-t pt-4 print-border" style="page-break-inside: avoid; break-inside: avoid;">
                        <div>
                            <div><strong>สรุปยอดทะเบียน:</strong></div>
                            <div>นักเรียนทั้งหมด: ${totalStudents} คน &nbsp;&nbsp;|&nbsp;&nbsp; ลงทะเบียนแล้ว: <span class="font-bold text-emerald-600">${registeredCount}</span> คน &nbsp;&nbsp;|&nbsp;&nbsp; ยังไม่ลงทะเบียน: <span class="font-bold text-red-500">${unregisteredCount}</span> คน</div>
                            <div class="text-[0.75em] opacity-75 mt-2">พิมพ์เมื่อ: ${printDate}</div>
                        </div>

                        ${showSignature ? `
                        <div class="text-center self-end w-64 mr-4 no-print-break">
                            <div class="border-b border-gray-400 w-full mb-2 h-12"></div>
                            <div>
                                <div>ลงชื่อ ...................................................... ผู้ตรวจสอบ</div>
                                <div class="text-[0.75em] opacity-75 mt-1">( เจ้าหน้าที่ / นายทะเบียน )</div>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                `;

                renderArea.appendChild(sheet);
            });
        }

        // Handle level selection changes to dynamically adjust room listings
        filterLevel.addEventListener('change', function() {
            updateRoomOptions();
            renderSheets();
        });

        filterRoom.addEventListener('change', renderSheets);

        // Attach event listeners for real-time config updates
        [groupingSelect, customTitleInput, fontSizeRange, colIdCheck, colClassCheck, colClubCheck, colAdvisorCheck, colRemarksCheck, showSignatureCheck].forEach(el => {
            el.addEventListener('change', renderSheets);
            el.addEventListener('input', renderSheets);
        });

        // Initialize view
        filterLevel.value = initLevel;
        updateRoomOptions();
        filterRoom.value = initRoom;
        renderSheets();
    </script>
</body>
</html>
