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
            <h1 class="m-0"><?php echo $global['nameschool']; ?> <span class="text-blue-600">| ครู</span></h1>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <section class="content">
            <div class="container-fluid">
                <!-- เนื้อหาสำหรับครู -->
                <div class="alert alert-success"> ยินดีต้อนรับเข้าสู่ระบบ
                </div>
                <!-- คู่มือการใช้งานสำหรับครู -->
            <div class="mb-6 max-w-6xl mx-auto bg-yellow-50 border-l-4 border-yellow-400 rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-yellow-700 mb-3 flex items-center gap-2">
                    📚 วิธีใช้งานหน้ารายการชุมนุมสำหรับครู
                </h2>
                <ul class="list-disc list-inside space-y-2 text-gray-800">
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 text-lg">🔎</span>
                        <span>
                            <b>ดูรายการชุมนุม</b> — ตารางจะแสดงชุมนุมทั้งหมดที่เปิดในปีการศึกษานี้ พร้อมรายละเอียดครบถ้วน
                        </span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-500 text-lg">➕</span>
                        <span>
                            <b>สร้างชุมนุมใหม่</b> — กดปุ่ม <span class="bg-blue-600 text-white px-2 py-1 rounded">+ สร้างชุมนุม</span> เพื่อเพิ่มชุมนุมใหม่ กรอกข้อมูลให้ครบถ้วนแล้วกด <span class="bg-blue-600 text-white px-2 py-1 rounded">บันทึก</span>
                        </span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-yellow-500 text-lg">✏️</span>
                        <span>
                            <b>แก้ไขชุมนุม</b> — กดปุ่ม <span class="bg-yellow-400 text-white px-2 py-1 rounded">แก้ไข</span> ในแถวของชุมนุมที่ต้องการ แล้วปรับข้อมูลตามต้องการ
                        </span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-500 text-lg">🗑️</span>
                        <span>
                            <b>ลบชุมนุม</b> — กดปุ่ม <span class="bg-red-500 text-white px-2 py-1 rounded">ลบ</span> ในแถวของชุมนุมที่ต้องการ หากไม่มีสมาชิกในชุมนุมจะสามารถลบได้
                        </span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-purple-500 text-lg">🎯</span>
                        <span>
                            <b>กรองระดับชั้น</b> — ใช้เมนู <span class="bg-gray-200 px-2 py-1 rounded">ระดับชั้น</span> ด้านบนซ้ายเพื่อดูเฉพาะชุมนุมที่เปิดรับระดับชั้นที่ต้องการ
                        </span>
                    </li>
                </ul>
                <div class="mt-4 text-blue-700 flex items-center gap-2">
                    <span>💡</span>
                    <span>คุณครูสามารถแก้ไข/ลบได้เฉพาะชุมนุมที่ตนเองเป็นที่ปรึกษาเท่านั้น</span>
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
