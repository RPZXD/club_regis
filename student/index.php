<?php 
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'นักเรียน') {
    header('Location: ../login.php');
    exit;
}
$user = $_SESSION['user'];

// เปิด error reporting สำหรับ debug (แนะนำให้ปิดใน production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Read configuration from JSON file
$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

// ดึงค่า term และ pee จาก session
$term = isset($_SESSION['term']) ? $_SESSION['term'] : '-';
$pee = isset($_SESSION['pee']) ? $_SESSION['pee'] : '-';

require_once('header.php');

?>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <?php require_once('wrapper.php');?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Student Dashboard</h1>
                        <!-- แสดงปีการศึกษาและภาคเรียน -->
                        <div class="mt-2 text-gray-700 text-base">
                            ปีการศึกษา: <span class="font-semibold text-blue-700"><?php echo htmlspecialchars($pee); ?></span>
                            | ภาคเรียน: <span class="font-semibold text-blue-700"><?php echo htmlspecialchars($term); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <!-- เนื้อหาสำหรับนักเรียน -->
                <div class="alert alert-success">
                    สวัสดีนักเรียน <?php echo $user['Stu_name'] . ' ' . $user['Stu_sur']; ?> ยินดีต้อนรับเข้าสู่ระบบ
                </div>
                <!-- ขั้นตอนการสมัครชุมนุม -->
                <div class="mt-6 max-w-2xl mx-auto bg-white rounded-lg shadow p-6 border border-blue-200">
                    <h2 class="text-xl font-bold text-blue-700 mb-4 flex items-center gap-2">
                        📝 ขั้นตอนการสมัครเข้าร่วมชุมนุม
                    </h2>
                    <ol class="list-decimal list-inside space-y-2 text-gray-700">
                        <li class="flex items-start gap-2">
                            <span class="text-blue-500 text-lg">🔍</span>
                            <span>
                                <b>ดูรายชื่อชุมนุม</b> เลือกดูว่ามีชุมนุมอะไรบ้างที่เปิดรับสมัครในปีนี้
                            </span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-green-500 text-lg">✅</span>
                            <span>
                                <b>เลือกชุมนุมที่สนใจ</b> แล้วกดปุ่ม <span class="font-semibold text-blue-600">"สมัคร"</span> ข้างชุมนุมนั้น
                            </span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-500 text-lg">📝</span>
                            <span>
                                <b>ยืนยันการสมัคร</b> ระบบจะถามยืนยัน ให้กด "สมัคร" อีกครั้งเพื่อยืนยัน
                            </span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-purple-500 text-lg">📄</span>
                            <span>
                                <b>รอครูที่ปรึกษาตรวจสอบ</b> เมื่อสมัครแล้ว รอครูที่ปรึกษาชุมนุมอนุมัติ
                            </span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-pink-500 text-lg">🎉</span>
                            <span>
                                <b>เสร็จสิ้น!</b> เมื่อได้รับการอนุมัติ สามารถเข้าร่วมกิจกรรมของชุมนุมได้เลย
                            </span>
                        </li>
                    </ol>
                    <div class="mt-4 text-blue-600 flex items-center gap-2">
                        <span>💡</span>
                        <span>
                            <b>หมายเหตุ:</b> สมัครได้เพียง 1 ชุมนุมต่อปี หากมีข้อสงสัย ติดต่อครูที่ปรึกษาชุมนุมหรือฝ่ายกิจกรรมพัฒนาผู้เรียน
                        </span>
                    </div>
                </div>
                <!-- เพิ่มเนื้อหาอื่นๆที่ต้องการ -->
            </div>
        </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
    <?php require_once('../footer.php');?>
</div>
<!-- ./wrapper -->


<script>

</script>
<?php require_once('script.php');?>
</body>
</html>
