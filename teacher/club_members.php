<?php
session_start();
// เช็ค session และ role
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'ครู') {
    header('Location: ../login.php');
    exit;
}
// Read configuration from JSON file
$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];
$user = $_SESSION['user'];
$teacher_id = $user['Teach_id'] ?? $_SESSION['Teach_id'];

require_once('../models/TermPee.php');

require_once('header.php');
?>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">
    <?php require_once('wrapper.php');?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">จัดการสมาชิกในชุมนุม</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header bg-blue-600 text-white font-semibold text-lg">
                        <form id="club-select-form" class="flex items-center gap-2" onsubmit="return false;">
                            <label for="club_id" class="font-medium">เลือกชุมนุม:</label>
                            <select name="club_id" id="club_id" class="border text-black border-gray-300 rounded p-2 w-60">
                                <option class="text-center text-black" value="">-- เลือกชุมนุม --</option>
                                <!-- Clubs will be loaded here via JS -->
                            </select>
                            <button type="button" id="print-btn" class="ml-4 btn bg-yellow-400 text-gray-700 hover:bg-yellow-600 hover:text-gray-900" >
                                <i class="fa fa-print"></i> พิมพ์รายชื่อ
                            </button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="overflow-x-auto">
                            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
                            <table id="members-table" class="min-w-full bg-white border border-gray-200 rounded shadow display">
                                <thead>
                                    <tr class="bg-blue-500 text-white font-semibold">
                                        <th class="py-2 px-4 text-center border-b">ลำดับ</th>
                                        <th class="py-2 px-4 text-center border-b">รหัสนักเรียน</th>
                                        <th class="py-2 px-4 text-center border-b">ชื่อนักเรียน</th>
                                        <th class="py-2 px-4 text-center border-b">ชั้น</th>
                                        <th class="py-2 px-4 text-center border-b">เวลาที่สมัคร</th>
                                        <th class="py-2 px-4 text-center border-b">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="members-table-body">
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
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
let clubInfoCache = {}; // เก็บข้อมูลชุมนุมที่เลือกไว้สำหรับพิมพ์

document.addEventListener('DOMContentLoaded', function() {
    // โหลดเฉพาะชุมนุมที่ครูที่ปรึกษาดูแล
    fetch('../controllers/ClubController.php?action=list_by_advisor')
        .then(res => res.json())
        .then(data => {
            if (data.data && Array.isArray(data.data)) {
                const select = document.getElementById('club_id');
                data.data.forEach(club => {
                    const opt = document.createElement('option');
                    opt.value = club.club_id;
                    opt.textContent = club.club_name;
                    select.appendChild(opt);
                    clubInfoCache[club.club_id] = club; // เก็บข้อมูลไว้
                });
            }
        });

    // DataTable instance
    let membersTable = $('#members-table').DataTable({
        "ordering": true,
        "order": [[4, "asc"]], // คอลัมน์เวลาที่สมัคร (index 4) ASC
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
        },
        "destroy": true // ให้สามารถรีอินิท DataTable ได้
    });

    // เมื่อเลือกชุมนุม ให้โหลดรายชื่อนักเรียน
    document.getElementById('club_id').addEventListener('change', function() {
        const clubId = this.value;
        // ล้างข้อมูล DataTable เดิม
        membersTable.clear().draw();
        if (!clubId) return;
        // ดึง term/year ปัจจุบันจาก ClubController (ใช้ promise chain)
        fetch('../controllers/ClubController.php?action=list_by_advisor')
            .then(res => res.json())
            .then(data => {
                const term = data.term;
                const year = data.year;
                return fetch('../controllers/ClubController.php?action=members&club_id=' + encodeURIComponent(clubId) + '&term=' + encodeURIComponent(term) + '&year=' + encodeURIComponent(year));
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && Array.isArray(data.members)) {
                    // sort by created_at ASC
                    data.members.sort((a, b) => {
                        if (!a.created_at) return -1;
                        if (!b.created_at) return 1;
                        return a.created_at.localeCompare(b.created_at);
                    });
                    data.members.forEach((stu, idx) => {
                        membersTable.row.add([
                            idx + 1,
                            stu.student_id,
                            stu.name,
                            stu.class_name || '',
                            stu.created_at ? stu.created_at : '-',
                            `<button class="btn btn-danger btn-sm delete-member" data-student-id="${stu.student_id}" data-club-id="${clubId}">ลบ</button>`
                        ]).draw(false);
                    });
                } else {
                    membersTable.row.add([
                        '', '', 'ไม่พบข้อมูล', '', '', ''
                    ]).draw(false);
                }
            });
    });

    document.getElementById('members-table-body').addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-member')) {
            const studentId = event.target.getAttribute('data-student-id');
            const clubId = event.target.getAttribute('data-club-id');
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: 'คุณต้องการลบสมาชิกคนนี้ออกจากชุมนุมหรือไม่',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ใช่, ลบเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('../controllers/ClubController.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: new URLSearchParams({
                            action: 'delete_member',
                            student_id: studentId,
                            club_id: clubId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ',
                                showConfirmButton: false,
                                timer: 1200
                            });
                            document.getElementById('club_id').dispatchEvent(new Event('change'));
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: data.message || ''
                            });
                        }
                    });
                }
            });
        }
    });

    // พิมพ์รายชื่อ
    document.getElementById('print-btn').addEventListener('click', function() {
        const clubId = document.getElementById('club_id').value;
        if (!clubId) {
            Swal.fire({icon: 'warning', title: 'กรุณาเลือกชุมนุมก่อนพิมพ์'});
            return;
        }
        const club = clubInfoCache[clubId];
        const tbody = document.getElementById('members-table-body');
        // ดึงข้อมูลนักเรียนจากตาราง
        let rows = [];
        tbody.querySelectorAll('tr').forEach(tr => {
            let cells = Array.from(tr.children);
            // ข้ามแถว "ไม่พบข้อมูล"
            if (cells.length < 6) return;
            rows.push([
                cells[0].textContent,
                cells[1].textContent,
                cells[2].textContent,
                cells[3].textContent,
                '' // ช่องหมายเหตุ
            ]);
        });

        // หัวตารางใหม่
        const tableHead = `
            <thead>
                <tr class="bg-blue-500 text-white font-semibold">
                    <th class="py-2 px-4 text-center border-b">ลำดับ</th>
                    <th class="py-2 px-4 text-center border-b">รหัสนักเรียน</th>
                    <th class="py-2 px-4 text-center border-b">ชื่อนักเรียน</th>
                    <th class="py-2 px-4 text-center border-b">ชั้น</th>
                    <th class="py-2 px-4 text-center border-b">หมายเหตุ</th>
                </tr>
            </thead>
        `;

        // สร้างแถวข้อมูล
        let tableBody = '<tbody>';
        if (rows.length > 0) {
            rows.forEach(row => {
                tableBody += `<tr>
                    <td class="py-2 px-4 text-center border-b">${row[0]}</td>
                    <td class="py-2 px-4 text-center border-b">${row[1]}</td>
                    <td class="py-2 px-4 border-b">${row[2]}</td>
                    <td class="py-2 px-4 text-center border-b">${row[3]}</td>
                    <td class="py-2 px-4 text-center border-b"></td>
                </tr>`;
            });
        } else {
            tableBody += `<tr><td colspan="5" class="text-center py-2">ไม่พบข้อมูล</td></tr>`;
        }
        tableBody += '</tbody>';

        // หัวกระดาษ
        const advisorName = club.advisor_teacher_name || '';
        const advisorTel = club.advisor_teacher_tel || '';
        const clubName = club.club_name || '';
        const headerHtml = `
            <div style="margin-bottom:16px; text-align: center;">
                <div style="font-size:1.2em;font-weight:bold;">ชื่อชุมนุม ${clubName}</div>
                <div style="font-size:1em;">ครูที่ปรึกษา ${advisorName} โทร ${advisorTel ? advisorTel : '................................'}</div>
            </div>
        `;

        function space(n) {
            return '&nbsp;'.repeat(n);
        }

        // ท้ายกระดาษ
        const footerHtml = `
        <div style="margin-top:40px; text-align:right;">
            <div style="display: inline-block; text-align: center; width:400px;">
                ลงชื่อ......................................................ครูที่ปรึกษาชุมนุม<br>
                (${advisorName})<br>
                วันที่ ${new Date().toLocaleDateString('th-TH', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                })}
            </div>
        </div>
    `;

        // CSS สำหรับพิมพ์ (คัดลอกจาก DataTable/Bootstrap/Tailwind ที่ใช้)
        const style = `
            <style>
                body { font-family: 'TH SarabunPSK', 'Sarabun', Arial, sans-serif; margin: 30px; }
                table { border-collapse: collapse; width: 100%; font-size: 1.1em; }
                th, td { border: 1px solid #cbd5e1; padding: 8px 12px; }
                th { background: #2563eb; color: #fff; }
                tr:nth-child(even) { background: #f1f5f9; }
                .text-center { text-align: center; }
                .border-b { border-bottom: 1px solid #cbd5e1; }
                .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
                .px-4 { padding-left: 1rem; padding-right: 1rem; }
                @media print {
                    button, .btn, .no-print { display: none !important; }
                }
            </style>
        `;

        // HTML ทั้งหมด
        const html = `
            <html>
            <head>
                <title>พิมพ์รายชื่อชุมนุม</title>
                ${style}
            </head>
            <body>
                ${headerHtml}
                <table>
                    ${tableHead}
                    ${tableBody}
                </table>
                ${footerHtml}
            </body>
            </html>
        `;

        // เปิดหน้าต่างใหม่แล้วพิมพ์
        const printWindow = window.open('', '', 'width=900,height=700');
        printWindow.document.write(html);
        printWindow.document.close();
        setTimeout(() => {
            printWindow.focus();
            printWindow.print();
        }, 400);
    });
});
</script>
</body>
</html>
