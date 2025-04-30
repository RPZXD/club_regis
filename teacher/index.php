<?php
require_once('../includes/header.php');
require_once('../utils/Utils.php');

// ตรวจสอบ session เพื่อป้องกันการเข้าถึงโดยไม่ได้ login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['Teacher_login'])) {
    header('Location: ../login.php');
    exit();
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
                        <h1 class="m-0">Teacher Dashboard</h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <!-- เนื้อหาสำหรับครู -->
                <div class="alert alert-success">
                    สวัสดีคุณครู <?php echo htmlspecialchars($_SESSION['user']); ?> ยินดีต้อนรับเข้าสู่ระบบ
                </div>
                <!-- เพิ่มเนื้อหาอื่นๆที่ต้องการ -->
            </div>
        </section>
    </div>
    <?php require_once('../includes/footer.php'); ?>
</div>
<?php require_once('../includes/script.php'); ?>
</body>
</html>
