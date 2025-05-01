<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'นักเรียน') {
    header('Location: ../login.php');
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-green-400 to-blue-500 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md text-center">
        <h1 class="text-2xl font-bold text-blue-700 mb-4">ยินดีต้อนรับนักเรียน</h1>
        <div class="mb-4">
            <span class="block text-lg font-medium text-gray-700">รหัสนักเรียน: <?php echo htmlspecialchars($user['Stu_id']); ?></span>
            <span class="block text-lg font-medium text-gray-700">ชื่อ: <?php echo htmlspecialchars($user['Stu_name'] . ' ' . $user['Stu_sur']); ?></span>
        </div>
        <?php if (!empty($user['Stu_picture'])): ?>
            <img src="http://std.phichai.ac.th/photo/<?php echo htmlspecialchars($user['Stu_picture']); ?>" alt="Student Photo" class="mx-auto rounded-full h-24 w-24 border mb-4">
        <?php endif; ?>
        <a href="../logout.php" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">ออกจากระบบ</a>
    </div>
</body>
</html>
