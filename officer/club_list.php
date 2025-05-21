<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    header('Location: ../login.php');
    exit;
}
$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];
require_once('header.php');
?>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">
    <?php require_once('wrapper.php');?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h5 class="m-0">รายการชุมนุมทั้งหมด</h5>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header bg-blue-600 text-white font-semibold text-lg">
                        รายการชุมนุม
                    </div>
                    <div class="card-body">
                        <!-- Grade filter -->
                        <div id="grade-filter-container" class="mb-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-semibold text-blue-700 mr-2">กรองระดับชั้น:</span>
                                <button id="grade-select-all" class="px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-sm transition">เลือกทั้งหมด</button>
                                <button id="grade-unselect-all" class="px-2 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm transition">ยกเลิกทั้งหมด</button>
                                <div id="grade-checkboxes" class="flex flex-wrap items-center gap-2"></div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
                            <table id="club-table" class="min-w-full bg-white border border-gray-200 rounded shadow display">
                                <thead>
                                    <tr class="bg-blue-500 text-white font-semibold">
                                        <th class="py-2 px-4 text-center border-b">รหัส</th>
                                        <th class="py-2 px-4 text-center border-b">ชื่อชุมนุม</th>
                                        <th class="py-2 px-4 text-center border-b">รายละเอียด</th>
                                        <th class="py-2 px-4 text-center border-b">ครูที่ปรึกษา</th>
                                        <th class="py-2 px-4 text-center border-b">ระดับชั้น</th>
                                        <th class="py-2 px-4 text-center border-b">จำนวนที่รับ</th>
                                        <th class="py-2 px-4 text-center border-b">ปี/เทอม</th>
                                        <th class="py-2 px-4 text-center border-b">พิมพ์รายชื่อ</th>
                                    </tr>
                                </thead>
                                <tbody id="club-table-body">
                                    <!-- DataTables will populate here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php');?>
</div>
<?php require_once('script.php');?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
let allClubs = [];
let table;
let gradeLevelsSet = new Set();

function getGradeLevelsArray(grade_levels) {
    // แยกด้วย , หรือ / หรือเว้นวรรค
    return grade_levels.split(/[,/ ]+/).map(s => s.trim()).filter(Boolean);
}

function renderGradeFilter(gradeLevelsCount) {
    const container = document.getElementById('grade-checkboxes');
    container.innerHTML = '';
    Object.entries(gradeLevelsCount).sort().forEach(([grade, count]) => {
        const id = 'grade-filter-' + grade.replace(/\W/g, '');
        container.innerHTML += `
            <label for="${id}" class="flex items-center gap-1 px-2 py-1 bg-blue-50 rounded hover:bg-blue-100 cursor-pointer transition text-sm">
                <input type="checkbox" id="${id}" class="grade-filter-checkbox accent-blue-600" value="${grade}" checked>
                <span>${grade}</span>
                <span class="text-xs text-blue-500 bg-blue-100 rounded px-1">${count}</span>
            </label>
        `;
    });
}

function filterTableByGrade() {
    const checked = Array.from(document.querySelectorAll('.grade-filter-checkbox:checked')).map(cb => cb.value);
    table.rows().every(function() {
        const rowData = this.data();
        // rowData[4] = grade_levels
        const clubGrades = getGradeLevelsArray(
            rowData[4].replace(/<[^>]+>/g, '') // remove html
        );
        // ถ้ามีอย่างน้อย 1 grade ที่ตรงกับ filter ให้แสดง
        const show = clubGrades.some(g => checked.includes(g));
        $(this.node()).toggle(show);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    fetch('../controllers/ClubController.php?action=list')
        .then(res => res.json())
        .then(data => {
            if (data.data && Array.isArray(data.data)) {
                allClubs = data.data;
                // สร้าง set ของ grade_levels ทั้งหมด
                let gradeLevelsCount = {};
                allClubs.forEach(club => {
                    getGradeLevelsArray(club.grade_levels).forEach(g => {
                        gradeLevelsSet.add(g);
                        gradeLevelsCount[g] = (gradeLevelsCount[g] || 0) + 1;
                    });
                });
                renderGradeFilter(gradeLevelsCount);

                table = $('#club-table').DataTable({
                    "ordering": true,
                    "order": [[0, "asc"]],
                    "language": {
                        "lengthMenu": "แสดง _MENU_ รายการ",
                        "zeroRecords": "ไม่พบข้อมูล",
                        "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
                        "infoEmpty": "ไม่มีข้อมูล",
                        "infoFiltered": "(กรองจาก _MAX_ รายการทั้งหมด)",
                        "search": "ค้นหา:",
                        "paginate": {
                            "first": "แรก",
                            "last": "สุดท้าย",
                            "next": "ถัดไป",
                            "previous": "ก่อนหน้า"
                        }
                    }
                });
                data.data.forEach(club => {
                    // Progress bar for จำนวนที่รับ
                    const max = parseInt(club.max_members) || 0;
                    const current = parseInt(club.current_members_count) || 0;
                    const percent = max > 0 ? Math.round((current / max) * 100) : 0;
                    let barColor = 'bg-green-400';
                    let emoji = '✅';
                    if (percent >= 100) {
                        barColor = 'bg-red-500';
                        emoji = '⛔';
                    } else if (percent >= 70) {
                        barColor = 'bg-yellow-400';
                        emoji = '⚠️';
                    }
                    const progressBar = `
                        <div class="w-36 mx-auto">
                            <div class="relative h-5 bg-gray-200 rounded-full overflow-hidden shadow-inner">
                                <div class="absolute left-0 top-0 h-5 ${barColor} rounded-full transition-all duration-500" style="width: ${percent}%;"></div>
                                <div class="absolute w-full text-xs text-center top-0 left-0 h-5 leading-5 font-bold text-blue-900 select-none">
                                    ${current} / ${max} ${emoji}
                                </div>
                            </div>
                        </div>
                    `;

                    // Tooltip for club description
                    const desc = `<span title="${club.description.replace(/"/g, '&quot;')}">${club.description.length > 40 ? club.description.substring(0, 40) + '...' : club.description}</span>`;

                    // ปุ่มพิมพ์รายชื่อ
                    const printBtn = `
                        <a href="print_club.php?club_id=${club.club_id}" target="_blank" 
                           class="inline-block px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-700 transition"
                           title="พิมพ์รายชื่อ">
                            <i class="fas fa-print"></i> พิมพ์
                        </a>
                    `;

                    table.row.add([
                        club.club_id,
                        `<span class="font-semibold text-blue-700">${club.club_name}</span>`,
                        desc,
                        `<span class="text-indigo-700">${club.advisor_teacher_name || club.advisor_teacher}</span>`,
                        `<span class="text-indigo-500">${club.grade_levels}</span>`,
                        progressBar,
                        `<span class="font-mono">${club.year ? club.year : ''}${club.term ? ' / ' + club.term : ''}</span>`,
                        printBtn // เพิ่มปุ่มพิมพ์ในคอลัมน์สุดท้าย
                    ]).draw(false);
                });

                // Event: filter by grade
                document.getElementById('grade-checkboxes').addEventListener('change', filterTableByGrade);

                // Select all / Unselect all
                document.getElementById('grade-select-all').onclick = function() {
                    document.querySelectorAll('.grade-filter-checkbox').forEach(cb => cb.checked = true);
                    filterTableByGrade();
                };
                document.getElementById('grade-unselect-all').onclick = function() {
                    document.querySelectorAll('.grade-filter-checkbox').forEach(cb => cb.checked = false);
                    filterTableByGrade();
                };
            }
        });
});
</script>
</body>
</html>
