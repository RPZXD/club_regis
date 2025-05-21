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
<!-- Tailwind CSS CDN -->


<body class="hold-transition sidebar-mini layout-fixed light-mode bg-gray-100">
<div class="wrapper">
    <?php require_once('wrapper.php');?>

    <div class="content-wrapper bg-white rounded-lg shadow-lg mt-6 mx-auto max-w-6xl">
        <div class="content-header">
            <div class="container-fluid">
                <h5 class="m-0 py-4 text-2xl font-bold text-center text-blue-700">รายงานกิจกรรมชมรม</h5>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <!-- Nav tabs -->
                <ul class="flex border-b mb-4" id="reportTabs" role="tablist">
                    <li class="mr-2">
                        <a class="nav-link active inline-block px-6 py-2 rounded-t-lg font-semibold text-blue-700 bg-blue-100 transition-all duration-200 ease-in-out hover:bg-blue-200 focus:outline-none" id="room-tab" data-toggle="tab" href="#room" role="tab" aria-controls="room" aria-selected="true">
                            <svg class="inline w-5 h-5 mr-1 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 17l4 4 4-4m-4-5v9"></path><path d="M20.24 12.24A9 9 0 1 0 21 12"></path></svg>
                            รายห้อง
                        </a>
                    </li>
                    <li class="mr-2">
                        <a class="nav-link inline-block px-6 py-2 rounded-t-lg font-semibold text-gray-700 bg-gray-100 transition-all duration-200 ease-in-out hover:bg-blue-100 focus:outline-none" id="level-tab" data-toggle="tab" href="#level" role="tab" aria-controls="level" aria-selected="false">
                            <svg class="inline w-5 h-5 mr-1 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 10h18M3 6h18M3 14h18M3 18h18"></path></svg>
                            รายชั้น
                        </a>
                    </li>
                    <li>
                        <a class="nav-link inline-block px-6 py-2 rounded-t-lg font-semibold text-gray-700 bg-gray-100 transition-all duration-200 ease-in-out hover:bg-blue-100 focus:outline-none" id="overview-tab" data-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="false">
                            <svg class="inline w-5 h-5 mr-1 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M8 12l2 2 4-4"></path></svg>
                            ภาพรวมทั้งโรงเรียน
                        </a>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active animate-fade-in" id="room" role="tabpanel" aria-labelledby="room-tab">
                        <!-- เนื้อหารายห้อง -->
                        <div class="p-6 bg-blue-50 rounded-lg shadow-inner transition-all duration-300">
                            <p class="text-lg font-medium text-blue-800 mb-4">รายงานตามห้องเรียน</p>
                            <div class="flex flex-wrap gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-1" for="select-level">เลือกชั้น</label>
                                    <select id="select-level" class="block w-32 px-3 py-2 border border-blue-300 rounded focus:ring focus:ring-blue-200">
                                        <option value="">-- เลือกชั้น --</option>
                                        <option value="ม.1">ม.1</option>
                                        <option value="ม.2">ม.2</option>
                                        <option value="ม.3">ม.3</option>
                                        <option value="ม.4">ม.4</option>
                                        <option value="ม.5">ม.5</option>
                                        <option value="ม.6">ม.6</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-1" for="select-room">เลือกห้อง</label>
                                    <select id="select-room" class="block w-32 px-3 py-2 border border-blue-300 rounded focus:ring focus:ring-blue-200" disabled>
                                        <option value="">-- เลือกห้อง --</option>
                                    </select>
                                </div>
                            </div>
                            <div id="room-table-container">
                                <!-- ตารางจะแสดงตรงนี้ -->
                                <div class="text-gray-400 text-center py-8">กรุณาเลือกชั้นและห้อง</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade animate-fade-in" id="level" role="tabpanel" aria-labelledby="level-tab">
                        <!-- เนื้อหารายชั้น -->
                        <div class="p-6 bg-green-50 rounded-lg shadow-inner transition-all duration-300">
                            <p class="text-lg font-medium text-green-800 mb-4">รายงานตามชั้นเรียน</p>
                            <div class="flex flex-wrap gap-4 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-green-700 mb-1" for="select-level2">เลือกชั้น</label>
                                    <select id="select-level2" class="block w-32 px-3 py-2 border border-green-300 rounded focus:ring focus:ring-green-200">
                                        <option value="">-- เลือกชั้น --</option>
                                        <option value="ม.1">ม.1</option>
                                        <option value="ม.2">ม.2</option>
                                        <option value="ม.3">ม.3</option>
                                        <option value="ม.4">ม.4</option>
                                        <option value="ม.5">ม.5</option>
                                        <option value="ม.6">ม.6</option>
                                    </select>
                                </div>
                            </div>
                            <div id="level-table-container">
                                <div class="text-gray-400 text-center py-8">กรุณาเลือกชั้น</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade animate-fade-in" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        <!-- เนื้อหาภาพรวมทั้งโรงเรียน -->
                        <div class="p-6 bg-yellow-50 rounded-lg shadow-inner transition-all duration-300">
                            <p class="text-lg font-medium text-yellow-800">รายงานภาพรวมทั้งโรงเรียน</p>
                            <div class="mt-4 text-gray-600">[เนื้อหาหรือกราฟภาพรวม]</div>
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
<!-- Bootstrap JS (ถ้ายังไม่มี) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Tailwind tab switcher & animation -->
<script>
document.querySelectorAll('#reportTabs .nav-link').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        // Remove active classes
        document.querySelectorAll('#reportTabs .nav-link').forEach(t => {
            t.classList.remove('active', 'bg-blue-100', 'text-blue-700');
            t.classList.add('bg-gray-100', 'text-gray-700');
        });
        // Add active to clicked
        this.classList.add('active', 'bg-blue-100', 'text-blue-700');
        this.classList.remove('bg-gray-100', 'text-gray-700');
        // Switch tab content
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });
        const target = this.getAttribute('href');
        const pane = document.querySelector(target);
        if (pane) {
            pane.classList.add('show', 'active');
        }
    });
});
// Animation for fade-in
document.querySelectorAll('.tab-pane').forEach(pane => {
    pane.classList.add('transition-opacity', 'duration-300');
});
</script>
<style>
/* Simple fade-in animation */
.animate-fade-in {
    opacity: 0;
    transition: opacity 0.3s;
}
.tab-pane.active.show.animate-fade-in {
    opacity: 1;
}
</style>
<script>
// ตัวอย่างข้อมูลห้อง (ควร fetch จาก backend จริง)
const roomsByLevel = {
    "ม.1": [1,2,3,4,5,6,7,8,9,10,11,12],
    "ม.2": [1,2,3,4,5,6,7,8,9,10,11,12],
    "ม.3": [1,2,3,4,5,6,7,8,9,10,11,12],
    "ม.4": [1,2,3,4,5,6,7],
    "ม.5": [1,2,3,4,5,6,7],
    "ม.6": [1,2,3,4,5,6,7]
};

const selectLevel = document.getElementById('select-level');
const selectRoom = document.getElementById('select-room');
const tableContainer = document.getElementById('room-table-container');

selectLevel.addEventListener('change', function() {
    const level = this.value;
    selectRoom.innerHTML = '<option value="">-- เลือกห้อง --</option>';
    if (roomsByLevel[level]) {
        roomsByLevel[level].forEach(room => {
            selectRoom.innerHTML += `<option value="${room}">${room}</option>`;
        });
        selectRoom.disabled = false;
    } else {
        selectRoom.disabled = true;
    }
    tableContainer.innerHTML = '<div class="text-gray-400 text-center py-8">กรุณาเลือกชั้นและห้อง</div>';
});

selectRoom.addEventListener('change', function() {
    const level = selectLevel.value;
    const room = this.value;
    if (level && room) {
        fetchRoomData(level, room);
    } else {
        tableContainer.innerHTML = '<div class="text-gray-400 text-center py-8">กรุณาเลือกชั้นและห้อง</div>';
    }
});

function fetchRoomData(level, room) {
    tableContainer.innerHTML = '<div class="text-blue-400 text-center py-8 animate-pulse">กำลังโหลดข้อมูล...</div>';
    fetch('api/fetch_room_report.php?level=' + encodeURIComponent(level) + '&room=' + encodeURIComponent(room))
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                let html = `
                <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded shadow text-sm" id="room-report-table">
                    <thead>
                        <tr class="bg-blue-200 text-blue-900">
                            <th class="px-4 py-2 text-left">#</th>
                            <th class="px-4 py-2 text-left">เลขประจำตัว</th>
                            <th class="px-4 py-2 text-left">ชื่อ-สกุล</th>
                            <th class="px-4 py-2 text-left">ชั้น/ห้อง</th>
                            <th class="px-4 py-2 text-left">เลขที่</th>
                            <th class="px-4 py-2 text-left">ชุมนุมที่สมัคร</th>
                            <th class="px-4 py-2 text-left">ครูที่ปรึกษาชุมนุม</th>
                        </tr>
                    </thead>
                    <tbody>
                `;
                data.forEach((row, idx) => {
                    html += `
                        <tr class="hover:bg-blue-50">
                            <td class="px-4 py-2">${idx+1}</td>
                            <td class="px-4 py-2">${row.student_id}</td>
                            <td class="px-4 py-2">${row.fullname}</td>
                            <td class="px-4 py-2">${row.level}/${row.room}</td>
                            <td class="px-4 py-2">${row.number}</td>
                            <td class="px-4 py-2">${row.club}</td>
                            <td class="px-4 py-2">${row.advisor}</td>
                        </tr>
                    `;
                });
                html += `
                    </tbody>
                </table>
                </div>
                `;
                tableContainer.innerHTML = html;
                // Optional: DataTables init
                if (window.jQuery && window.jQuery.fn.dataTable) {
                    $('#room-report-table').DataTable({
                        "language": {
                            "lengthMenu": "แสดง _MENU_ แถว",
                            "zeroRecords": "ไม่พบข้อมูล",
                            "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ แถว",
                            "infoEmpty": "ไม่มีข้อมูล",
                            "infoFiltered": "(กรองจาก _MAX_ แถว)",
                            "search": "ค้นหา:",
                            "paginate": {
                                "first": "หน้าแรก",
                                "last": "หน้าสุดท้าย",
                                "next": "ถัดไป",
                                "previous": "ก่อนหน้า"
                            }
                        },
                        "order": [[0, "asc"]],
                        "pageLength": 10,
                        "lengthMenu": [5, 10, 25, 50, 100],
                        "pagingType": "simple",
                        "searching": true,
                        "info": true,
                        "autoWidth": false,
                        "responsive": true,
                        dom: '<"flex flex-wrap items-center justify-between mb-2"Bf>rt<"flex flex-wrap items-center justify-between mt-2"lip>',
                        buttons: [
                            {
                                extend: 'copy',
                                text: 'คัดลอก',
                                className: 'bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-1 px-3 rounded shadow mr-2 mb-2'
                            },
                            {
                                extend: 'excel',
                                text: 'ส่งออก Excel',
                                className: 'bg-green-200 hover:bg-green-300 text-green-800 font-semibold py-1 px-3 rounded shadow mr-2 mb-2'
                            },
                            {
                                extend: 'pdf',
                                text: 'ส่งออก PDF',
                                className: 'bg-red-200 hover:bg-red-300 text-red-800 font-semibold py-1 px-3 rounded shadow mr-2 mb-2'
                            },
                            {
                                extend: 'csv',
                                text: 'ส่งออก CSV',
                                className: 'bg-blue-200 hover:bg-blue-300 text-blue-800 font-semibold py-1 px-3 rounded shadow mr-2 mb-2'
                            },
                            {
                                extend: 'print',
                                text: 'พิมพ์',
                                className: 'bg-yellow-200 hover:bg-yellow-300 text-yellow-800 font-semibold py-1 px-3 rounded shadow mr-2 mb-2'
                            }
                        ],
                        drawCallback: function() {
                            // เพิ่ม Tailwind ให้กับฟิลด์ค้นหา/แสดงแถว
                            $('.dataTables_filter input').addClass('border border-blue-300 rounded px-2 py-1 ml-2');
                            $('.dataTables_length select').addClass('border border-blue-300 rounded px-2 py-1 ml-2');
                            $('.dt-buttons button').addClass('transition-all duration-150');
                        }
                    });
                }
            } else {
                tableContainer.innerHTML = '<div class="text-red-400 text-center py-8">ไม่พบข้อมูล</div>';
            }
        })
        .catch(() => {
            tableContainer.innerHTML = '<div class="text-red-400 text-center py-8">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
        });
}

// LEVEL TAB: รายงานตามชั้นเรียน
const selectLevel2 = document.getElementById('select-level2');
const levelTableContainer = document.getElementById('level-table-container');

selectLevel2.addEventListener('change', function() {
    const level = this.value;
    if (level) {
        fetchLevelData(level);
    } else {
        levelTableContainer.innerHTML = '<div class="text-gray-400 text-center py-8">กรุณาเลือกชั้น</div>';
    }
});

function fetchLevelData(level) {
    levelTableContainer.innerHTML = '<div class="text-green-400 text-center py-8 animate-pulse">กำลังโหลดข้อมูล...</div>';
    fetch('api/fetch_level_report.php?level=' + encodeURIComponent(level))
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                let html = `
                <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded shadow text-sm" id="level-report-table">
                    <thead>
                        <tr class="bg-green-200 text-green-900">
                            <th class="px-4 py-2 text-left">#</th>
                            <th class="px-4 py-2 text-left">ห้อง</th>
                            <th class="px-4 py-2 text-left">จำนวนนักเรียน</th>
                            <th class="px-4 py-2 text-left">ชุมนุมที่สมัครมากที่สุด</th>
                            <th class="px-4 py-2 text-left">จำนวนสมาชิก</th>
                        </tr>
                    </thead>
                    <tbody>
                `;
                data.forEach((row, idx) => {
                    html += `
                        <tr class="hover:bg-green-50">
                            <td class="px-4 py-2">${idx+1}</td>
                            <td class="px-4 py-2">${row.room}</td>
                            <td class="px-4 py-2">${row.student_count}</td>
                            <td class="px-4 py-2">${row.top_club}</td>
                            <td class="px-4 py-2">${row.top_club_count}</td>
                        </tr>
                    `;
                });
                html += `
                    </tbody>
                </table>
                </div>
                `;
                levelTableContainer.innerHTML = html;
                if (window.jQuery && window.jQuery.fn.dataTable) {
                    $('#level-report-table').DataTable({
                        "language": {
                            "lengthMenu": "แสดง _MENU_ แถว",
                            "zeroRecords": "ไม่พบข้อมูล",
                            "info": "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ แถว",
                            "infoEmpty": "ไม่มีข้อมูล",
                            "infoFiltered": "(กรองจาก _MAX_ แถว)",
                            "search": "ค้นหา:",
                            "paginate": {
                                "first": "หน้าแรก",
                                "last": "หน้าสุดท้าย",
                                "next": "ถัดไป",
                                "previous": "ก่อนหน้า"
                            }
                        },
                        "order": [[0, "asc"]],
                        "pageLength": 10,
                        "lengthMenu": [5, 10, 25, 50, 100],
                        "pagingType": "simple",
                        "searching": true,
                        "info": true,
                        "autoWidth": false,
                        "responsive": true,
                        dom: '<"flex flex-wrap items-center justify-between mb-2"Bf>rt<"flex flex-wrap items-center justify-between mt-2"lip>',
                        buttons: [
                            {
                                extend: 'copy',
                                text: 'คัดลอก',
                                className: 'bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-1 px-3 rounded shadow mr-2 mb-2'
                            },
                            {
                                extend: 'excel',
                                text: 'ส่งออก Excel',
                                className: 'bg-green-200 hover:bg-green-300 text-green-800 font-semibold py-1 px-3 rounded shadow mr-2 mb-2'
                            },
                            {
                                extend: 'pdf',
                                text: 'ส่งออก PDF',
                                className: 'bg-red-200 hover:bg-red-300 text-red-800 font-semibold py-1 px-3 rounded shadow mr-2 mb-2'
                            },
                            {
                                extend: 'csv',
                                text: 'ส่งออก CSV',
                                className: 'bg-blue-200 hover:bg-blue-300 text-blue-800 font-semibold py-1 px-3 rounded shadow mr-2 mb-2'
                            },
                            {
                                extend: 'print',
                                text: 'พิมพ์',
                                className: 'bg-yellow-200 hover:bg-yellow-300 text-yellow-800 font-semibold py-1 px-3 rounded shadow mr-2 mb-2'
                            }
                        ],
                        drawCallback: function() {
                            $('.dataTables_filter input').addClass('border border-green-300 rounded px-2 py-1 ml-2');
                            $('.dataTables_length select').addClass('border border-green-300 rounded px-2 py-1 ml-2');
                            $('.dt-buttons button').addClass('transition-all duration-150');
                        }
                    });
                }
            } else {
                levelTableContainer.innerHTML = '<div class="text-red-400 text-center py-8">ไม่พบข้อมูล</div>';
            }
        })
        .catch(() => {
            levelTableContainer.innerHTML = '<div class="text-red-400 text-center py-8">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
        });
}
</script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css"/>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
</body>
</html>
