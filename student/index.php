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
?>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('../includes/wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Student Dashboard</h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <!-- เนื้อหาสำหรับนักเรียน -->
                <div class="alert alert-success">
                    สวัสดีนักเรียน 
                    <?php
                        // ดึงชื่อจากฐานข้อมูล phichaia_student.student
                        require_once('../config/Database.php');
                        $studentId = $_SESSION['user'];
                        $db = new Database('phichaia_student');
                        $pdo = $db->getConnection();
                        $stmt = $pdo->prepare("SELECT Stu_pre, Stu_name, Stu_sur FROM student WHERE Stu_id = ?");
                        $stmt->execute([$studentId]);
                        $stu = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($stu) {
                            echo htmlspecialchars($stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur']);
                        } else {
                            echo htmlspecialchars($studentId);
                        }
                    ?> ยินดีต้อนรับเข้าสู่ระบบ
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
    </div>
    <?php require_once('../includes/footer.php'); ?>
</div>
<?php require_once('../includes/script.php'); ?>
</body>
</html>
