<?php
require_once('../includes/header.php');
require_once('../utils/Utils.php');

// ตรวจสอบ session เพื่อป้องกันการเข้าถึงโดยไม่ได้ login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['Student_login'])) {
    header('Location: ../login.php');
    exit();
}

// ดึง Stu_major (ระดับชั้น) ของนักเรียน
require_once('../config/Database.php');
$db = (new Database('phichaia_student'))->getConnection();
$stu_id = $_SESSION['user'];
$stmt = $db->prepare("SELECT Stu_major FROM student WHERE Stu_id = :stu_id LIMIT 1");
$stmt->bindParam(':stu_id', $stu_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$student_grade = $row ? $row['Stu_major'] : '';
// แปลง Stu_major (1,2,3,...) เป็น ม.1, ม.2, ...
$student_grade_text = '';
if ($student_grade !== '') {
    $student_grade_text = 'ม.' . intval($student_grade);
}

// ดึง club_id ที่สมัครแล้วใน term/pee ปัจจุบัน (ใช้ฐาน phichaia_club)
require_once('../models/TermPee.php');
$termPeeModel = new TermPeeModel();
$termpee = $termPeeModel->getTermPee();
$term = $termpee['term'];
$pee = $termpee['pee'];
$club_id_registered = null;
$db_club = (new Database('phichaia_club'))->getConnection();
$stmt = $db_club->prepare(
    "SELECT m.club_id 
     FROM club_members m
     INNER JOIN clubs c ON m.club_id = c.club_id
     WHERE m.student_id = :stu_id AND c.term = :term AND c.year = :pee
     LIMIT 1"
);
$stmt->bindParam(':stu_id', $stu_id);
$stmt->bindParam(':term', $term);
$stmt->bindParam(':pee', $pee);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row && isset($row['club_id'])) {
    $club_id_registered = $row['club_id'];
}
?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('../includes/wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0">สมัครเข้าชุมนุม</h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">

            <div class="card">
            <div class="card-body">
                <p class="text-gray-700 mb-4">เลือกชุมนุมที่ต้องการสมัคร</p>
                <div class="overflow-x-auto">
                    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
                    <table id="club-table" class="min-w-full bg-white border border-gray-200 rounded shadow display">
                        <thead>
                            <tr class="bg-blue-500 text-white font-semibold">
                                <th class="py-2 px-4 text-center border-b">ชื่อชุมนุม</th>
                                <th class="py-2 px-4 text-center border-b">รายละเอียด</th>
                                <th class="py-2 px-4 text-center border-b">ครูที่ปรึกษาชุมนุม</th>
                                <th class="py-2 px-4 text-center border-b">ระดับชั้นที่เปิด</th>
                                <th class="py-2 px-4 text-center border-b">จำนวนที่รับสมัคร</th>
                                <th class="py-2 px-4 text-center border-b">สถานะ</th>
                                <th class="py-2 px-4 text-center border-b">สมัคร</th>
                            </tr>
                        </thead>
                        <tbody id="club-table-body">
                            <!-- DataTables will populate here -->
                        </tbody>
                    </table>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                $(document).ready(function() {
                    // ระดับชั้นของนักเรียนจาก PHP (แปลงเป็น ม.x)
                    var studentGrade = <?php echo json_encode($student_grade_text); ?>;
                    // club_id ที่สมัครแล้วในเทอมนี้ (ถ้ามี)
                    var clubIdRegistered = <?php echo $club_id_registered ? json_encode($club_id_registered) : 'null'; ?>;

                    var table = $('#club-table').DataTable({
                        "ajax": "../controllers/ClubController.php?action=list",
                        "columns": [
                            { "data": "club_name", className: "text-center" },
                            { "data": "description" },
                            { "data": "advisor_teacher_name" },
                            { "data": "grade_levels", className: "text-center" },
                            { 
                                "data": null,
                                "render": function(data, type, row) {
                                    // Progress bar
                                    var percent = 0;
                                    if (parseInt(row.max_members) > 0) {
                                        percent = Math.round((parseInt(row.current_members) / parseInt(row.max_members)) * 100);
                                        if (percent > 100) percent = 100;
                                    }
                                    var progressBar = `
                                        <div style="min-width:100px">
                                            <div style="background:#e5e7eb;border-radius:4px;height:16px;overflow:hidden;">
                                                <div style="background:#2563eb;width:${percent}%;height:100%;transition:width 0.3s;" title="${row.current_members} / ${row.max_members}"></div>
                                            </div>
                                            <div style="font-size:0.85em;color:#444;text-align:right;">${row.current_members} / ${row.max_members}</div>
                                        </div>
                                    `;
                                    return progressBar;
                                },
                                "className": "text-center"
                            },
                            { 
                                "data": null,
                                "render": function(data, type, row) {
                                    if (parseInt(row.current_members) >= parseInt(row.max_members)) {
                                        return '<span class="text-red-600 font-semibold">เต็มแล้ว</span>';
                                    } else {
                                        return '<span class="text-green-600 font-semibold">ว่าง</span>';
                                    }
                                },
                                "className": "text-center"
                            },
                            {
                                "data": null,
                                "render": function(data, type, row) {
                                    // ถ้าสมัครแล้ว
                                    if (clubIdRegistered) {
                                        if (row.club_id === clubIdRegistered) {
                                            return '<span class="text-blue-600 font-semibold">สมัครเรียบร้อยแล้ว</span>';
                                        } else {
                                            return '';
                                        }
                                    }
                                    // ยังไม่ได้สมัคร
                                    if (parseInt(row.current_members) >= parseInt(row.max_members)) {
                                        return '<button class="bg-gray-400 text-white px-3 py-1 rounded cursor-not-allowed" disabled>สมัคร</button>';
                                    } else {
                                        return '<button class="register-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded" data-id="'+row.club_id+'">สมัคร</button>';
                                    }
                                },
                                "className": "text-center"
                            }
                        ],
                        "columnDefs": [
                            { "targets": 0, "width": "20%" },
                            { "targets": 1, "width": "30%" },
                            { "targets": 2, "width": "20%" },
                            { "targets": 3, "width": "10%" },
                            { "targets": 4, "width": "10%" },
                            { "targets": 5, "width": "10%" },
                            { "targets": 6, "width": "10%" }
                        ],
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
                        "pageLength": 10,
                        "lengthMenu": [10, 25, 50, 100],
                        "order": [[0, "asc"]],
                        "responsive": true,
                        "autoWidth": false,
                        "processing": true,
                        "initComplete": function(settings, json) {
                            // กรองข้อมูลทันทีหลังโหลด
                            table.draw();
                        }
                    });

                    // ฟังก์ชัน filter เฉพาะชุมนุมที่เปิดระดับชั้นของนักเรียน
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            var gradeLevels = data[3] || ""; // คอลัมน์ grade_levels
                            if (!studentGrade) return false;
                            // กรองโดยดูว่าระดับชั้นของนักเรียนอยู่ใน grade_levels หรือไม่
                            return gradeLevels.split(',').map(function(g){return g.trim();}).includes(studentGrade);
                        }
                    );

                    // สมัครชุมนุม
                    $('#club-table').on('click', '.register-btn', function() {
                        if (clubIdRegistered) {
                            Swal.fire({
                                icon: 'info',
                                title: 'คุณได้สมัครชมรมในเทอมนี้แล้ว',
                                text: 'ไม่สามารถสมัครซ้ำได้'
                            });
                            return;
                        }
                        var clubId = $(this).data('id');
                        Swal.fire({
                            title: 'ยืนยันการสมัคร',
                            text: 'คุณต้องการสมัครเข้าชุมนุมนี้ใช่หรือไม่?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'สมัคร',
                            cancelButtonText: 'ยกเลิก'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: '../controllers/StudentClubRegisterController.php?action=register',
                                    method: 'POST',
                                    data: { club_id: clubId },
                                    dataType: 'json',
                                    success: function(res) {
                                        if (res.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'สมัครสำเร็จ',
                                                text: res.message || 'คุณได้สมัครเข้าชุมนุมเรียบร้อยแล้ว'
                                            });
                                            // รีโหลดหน้าเพื่ออัปเดตปุ่ม
                                            setTimeout(function() {
                                                location.reload();
                                            }, 1200);
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'เกิดข้อผิดพลาด',
                                                text: res.message || 'เกิดข้อผิดพลาดในการสมัคร'
                                            });
                                        }
                                    },
                                    error: function() {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'เกิดข้อผิดพลาด',
                                            text: 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์'
                                        });
                                    }
                                });
                            }
                        });
                    });
                });
                </script>
            </div>
        </section>
    </div>
    <?php require_once('../includes/footer.php'); ?>
</div>
<?php require_once('../includes/script.php'); ?>
</body>
</html>
