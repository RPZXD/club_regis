<?php 
session_start();
// เช็ค session และ role
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    header('Location: ../login.php');
    exit;
}
// Read configuration from JSON file
$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

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
            <h1 class="m-0"><?php echo $global['nameschool']; ?> <span class="text-blue-600">| เจ้าหน้าที่</span></h1>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <section class="content">
            <div class="container-fluid">
                <!-- เนื้อหาสำหรับเจ้าหน้าที่ -->
                <div class="alert alert-success"> ยินดีต้อนรับเข้าสู่ระบบ
                </div>
                <!-- คู่มือการใช้งานสำหรับเจ้าหน้าที่ -->
            <div class="mb-6 max-w-6xl mx-auto bg-yellow-50 border-l-4 border-yellow-400 rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-yellow-700 mb-3 flex items-center gap-2">
                    📚 วิธีใช้งานหน้ารายการชุมนุมสำหรับเจ้าหน้าที่
                </h2>
                <ul class="list-disc list-inside space-y-2 text-gray-800">
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 text-lg">🔎</span>
                        <span>
                            <b>ดูรายการชุมนุม</b> — ตารางจะแสดงชุมนุมทั้งหมดที่เปิดในปีการศึกษานี้ พร้อมรายละเอียดครบถ้วน
                        </span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-500 text-lg">📝</span>
                        <span>
                            <b>จัดการข้อมูลชุมนุม</b> — เจ้าหน้าที่สามารถดูและจัดการข้อมูลชุมนุมทั้งหมดได้
                        </span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-yellow-500 text-lg">📋</span>
                        <span>
                            <b>รายงานและสถิติ</b> — ตรวจสอบรายงานและสถิติต่าง ๆ ที่เกี่ยวข้องกับการสมัครชุมนุม
                        </span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-purple-500 text-lg">🔒</span>
                        <span>
                            <b>สิทธิ์การเข้าถึง</b> — เจ้าหน้าที่สามารถดูข้อมูลทุกชุมนุม แต่ไม่สามารถแก้ไขข้อมูลของครูที่ปรึกษาได้
                        </span>
                    </li>
                </ul>
                <div class="mt-4 text-blue-700 flex items-center gap-2">
                    <span>💡</span>
                    <span>หากต้องการความช่วยเหลือเพิ่มเติม กรุณาติดต่อผู้ดูแลระบบ</span>
                </div>
            </div>
            <!-- จบคู่มือ -->
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
