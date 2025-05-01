<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'นักเรียน') {
    header('Location: ../login.php');
    exit;
}
$user = $_SESSION['user'];
$stu_major = isset($user['Stu_major']) ? $user['Stu_major'] : '';
$stu_grade = 'ม.' . $stu_major; // เพิ่มบรรทัดนี้

// โหลด config
$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

// ดึงข้อมูลชุมนุมจากฐานข้อมูล
require_once('../classes/DatabaseClub.php');
require_once('../models/Club.php');
require_once('../classes/DatabaseUsers.php'); // เพิ่มไฟล์นี้

use App\DatabaseClub;
use App\Models\Club;
use App\DatabaseUsers; // เพิ่ม use

$db = new DatabaseClub();
$pdo = $db->getPDO();
$clubModel = new Club($pdo);

// เพิ่มสำหรับดึงชื่อครูที่ปรึกษา
$dbUsers = new DatabaseUsers();

// ดึงเฉพาะชุมนุมที่เปิดรับสมัครในระดับชั้นของนักเรียน
$allClubs = $clubModel->getAll();
$clubs = [];
foreach ($allClubs as $club) {
    // grade_levels เป็น string เช่น "ม.1,ม.2,ม.3"
    $grades = array_map('trim', explode(',', $club['grade_levels']));
    if (in_array($stu_grade, $grades)) {
        // คำนวณจำนวนสมาชิกปัจจุบัน
        $currentMembers = $db->getCurrentMembers($club['club_id']);
        $club['current_members_count'] = count($currentMembers);

        // ดึงชื่อครูที่ปรึกษา
        $advisor = $dbUsers->query("SELECT Teach_name FROM teacher WHERE Teach_id = :id", ['id' => $club['advisor_teacher']])->fetch();
        $club['advisor_teacher_name'] = $advisor ? $advisor['Teach_name'] : $club['advisor_teacher'];

        $clubs[] = $club;
    }
}

require_once('header.php');
?>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">
    <?php require_once('wrapper.php');?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0 text-blue-700 font-bold">สมัครชุมนุม</h1>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="bg-white rounded-lg shadow-lg p-8 max-w-8xl mx-auto mt-8 border border-blue-200">
                    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-blue-700">
                        <span>🎉</span> เลือกชุมนุมที่เปิดรับสมัครสำหรับระดับชั้น 
                        <span class="text-blue-600 ml-2">ม.<?php echo htmlspecialchars($stu_major); ?></span>
                    </h2>
                    <div class="overflow-x-auto">
                        <table id="club-table" class="min-w-full border border-gray-200 rounded-lg shadow-sm bg-indigo-50">
                            <thead>
                                <tr class="bg-gradient-to-r from-indigo-200 to-indigo-100">
                                    <th class="py-3 px-4 border-b text-center font-semibold text-indigo-800">#️⃣ รหัส</th>
                                    <th class="py-3 px-4 border-b text-center font-semibold text-indigo-800">🏷️ ชื่อชุมนุม</th>
                                    <th class="py-3 px-4 border-b font-semibold text-indigo-800">📄 รายละเอียด</th>
                                    <th class="py-3 px-4 border-b text-center font-semibold text-indigo-800">👨‍🏫 ครูที่ปรึกษา</th>
                                    <th class="py-3 px-4 border-b text-center font-semibold text-indigo-800">🎓 ระดับชั้นที่เปิด</th>
                                    <th class="py-3 px-4 border-b text-center font-semibold text-indigo-800">👥 จำนวนที่รับสมัคร</th>
                                    <th class="py-3 px-4 border-b text-center font-semibold text-indigo-800">📝 สมัคร</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($clubs as $club): ?>
                                <tr class="hover:bg-indigo-100 transition">
                                    <td class="py-2 px-4 border-b text-center font-mono text-indigo-700"><?php echo htmlspecialchars($club['club_id']); ?></td>
                                    <td class="py-2 px-4 border-b text-center font-semibold text-indigo-900"><?php echo htmlspecialchars($club['club_name']); ?></td>
                                    <td class="py-2 px-4 border-b text-gray-700"><?php echo htmlspecialchars($club['description']); ?></td>
                                    <td class="py-2 px-4 border-b text-center text-indigo-600"><?php echo htmlspecialchars($club['advisor_teacher_name']); ?></td>
                                    <td class="py-2 px-4 border-b text-center text-indigo-500"><?php echo htmlspecialchars($club['grade_levels']); ?></td>
                                    <td class="py-2 px-4 border-b text-center">
                                        <?php
                                            $max = (int)$club['max_members'];
                                            $current = isset($club['current_members_count']) ? (int)$club['current_members_count'] : 0;
                                            $percent = $max > 0 ? round(($current / $max) * 100) : 0;
                                            $barColor = $percent < 70 ? 'bg-green-400' : ($percent < 100 ? 'bg-yellow-400' : 'bg-red-500');
                                        ?>
                                        <div class="w-36 mx-auto">
                                            <div class="relative h-5 bg-gray-200 rounded-full overflow-hidden shadow-inner">
                                                <div class="absolute left-0 top-0 h-5 <?= $barColor ?> rounded-full transition-all duration-500" style="width: <?= $percent ?>%;"></div>
                                                <div class="absolute w-full text-xs text-center top-0 left-0 h-5 leading-5 font-bold text-blue-900">
                                                    <?= $current ?> / <?= $max ?> 
                                                    <?= $percent >= 100 ? '⛔' : ($percent >= 70 ? '⚠️' : '✅') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2 px-4 border-b text-center">
                                        <button class="apply-btn bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white px-4 py-2 rounded-full shadow font-bold flex items-center gap-1 transition disabled:opacity-50"
                                            data-id="<?php echo $club['club_id']; ?>"
                                            <?= $percent >= 100 ? 'disabled' : '' ?>>
                                            <span>📝</span> สมัคร
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6 text-gray-700 text-base flex items-center gap-2 bg-blue-50 rounded p-4 border border-blue-100 shadow-sm">
                        <span>ℹ️</span>
                        <span>
                            <b>หมายเหตุ:</b> สมัครได้เพียง 1 ชุมนุมต่อปี หากมีข้อสงสัย ติดต่อครูที่ปรึกษาชุมนุมหรือฝ่ายกิจกรรมพัฒนาผู้เรียน
                        </span>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php');?>
</div>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    console.log(<?= json_encode($allClubs)?>); // debug ตรงนี้
    $('#club-table').DataTable({
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
    });

    // สมัครชุมนุม
    $(document).on('click', '.apply-btn', function() {
        var clubId = $(this).data('id');
        console.log('clubId:', clubId); // debug ตรงนี้
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
                    url: '../controllers/RegisClubController.php',
                    type: 'POST',
                    data: {
                        club_id: clubId
                        // year, term สามารถเพิ่มได้ถ้าต้องการส่งแบบ custom
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('สำเร็จ', res.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('ผิดพลาด', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                    }
                });
            }
        });
    });
});
</script>
<?php require_once('script.php');?>
</body>
</html>
