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
                    <?php
                        // ดึงชื่อจากฐานข้อมูล phichaia_student.teacher
                        require_once('../config/Database.php');
                        $TeacherId = $_SESSION['user'];
                        $db = new Database('phichaia_student');
                        $pdo = $db->getConnection();
                        $stmt = $pdo->prepare("SELECT Teach_name FROM teacher WHERE Teach_id = ?");
                        $stmt->execute([$TeacherId]);
                        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($teacher) {
                            $name = $teacher['Teach_name'];
                            $prefixes = [
                                'ว่าที่ร้อยตรี หญิง', // ต้องมาก่อน 'ว่าที่ร้อยตรี'
                                'ว่าที่ร้อยตรี',
                                'นาย', 'นาง', 'นางสาว', 'Mr. ', 'Mrs. ', 'Ms. '
                            ];
                            foreach ($prefixes as $prefix) {
                                if (mb_strpos($name, $prefix) === 0) {
                                    $name = trim(mb_substr($name, mb_strlen($prefix)));
                                    break;
                                }
                            }
                            echo 'สวัสดีคุณครู ' . htmlspecialchars($name);
                        } else {
                            echo 'สวัสดีคุณครู ' . htmlspecialchars($TeacherId);
                        }
                    ?> ยินดีต้อนรับเข้าสู่ระบบ
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
    </div>
    <?php require_once('../includes/footer.php'); ?>
</div>
<?php require_once('../includes/script.php'); ?>
</body>
</html>
