<?php
/**
 * Login Page - Club Registration System
 * Uses the new MVC layout with modern UI
 */
session_start();

// โหลด config
$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
$global = $config['global'];

// ตรวจสอบว่า login แล้วหรือยัง - redirect ตาม role
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $role = $_SESSION['role'] ?? '';
    $redirectMap = [
        'ครู' => 'teacher/index.php',
        'นักเรียน' => 'student/index.php',
        'เจ้าหน้าที่' => 'officer/index.php',
        'admin' => 'officer/index.php'
    ];
    if (isset($redirectMap[$role])) {
        header("Location: " . $redirectMap[$role]);
        exit();
    }
}

// เรียกใช้ LoginController
require_once __DIR__ . '/controllers/LoginController.php';

$error = null;
$loginSuccess = false;
$redirectUrl = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signin'])) {
    $input_username = $_POST['username'] ?? '';
    $input_password = $_POST['password'] ?? '';
    $input_role = $_POST['role'] ?? 'ครู';

    $controller = new LoginController();
    $error = $controller->login($input_username, $input_password, $input_role);
    
    if ($error === 'success') {
        $loginSuccess = true;
        $redirectUrl = $controller->getRedirectUrl($input_role);
    }
}

$pageTitle = 'เข้าสู่ระบบ';

// Render view with layout
ob_start();
require 'views/auth/login.php';
$content = ob_get_clean();
require 'views/layouts/app.php';
?>

<?php if (isset($error) && $error !== 'success' && $error !== null) { ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'เข้าสู่ระบบไม่สำเร็จ',
    text: <?= json_encode($error) ?>,
    confirmButtonText: 'ปิด',
    confirmButtonColor: '#3085d6'
});
</script>
<?php } ?>

<?php if (isset($_GET['logout']) && $_GET['logout'] == '1') { ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'ออกจากระบบสำเร็จ',
    text: 'คุณได้ออกจากระบบเรียบร้อยแล้ว',
    confirmButtonText: 'ตกลง',
    confirmButtonColor: '#3085d6'
});
</script>
<?php } ?>

<?php if ($loginSuccess) { ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'เข้าสู่ระบบสำเร็จ',
    text: 'กำลังเข้าสู่ระบบ...',
    showConfirmButton: false,
    timer: 1500
}).then(() => {
    window.location.href = <?= json_encode($redirectUrl) ?>;
});
</script>
<?php } ?>
