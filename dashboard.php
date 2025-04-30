<?php
// เปิด error reporting สำหรับ debug (ลบออกเมื่อขึ้น production จริง)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();
if (empty($_SESSION['logged_in']) || empty($_SESSION['role']) || empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$role = $_SESSION['role'];

// โหลด config
$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
$pageConfig = $config['global'];


function getRoleEdocName($role_edoc) {
    $map = [
        'DIR' => 'Director (ผู้อำนวยการ)',
        'VP' => 'Vice Principal (รองผู้อำนวยการ)',
        'HOD' => 'Head of Department (หัวหน้าแผนก/กลุ่มสาระ)',
        'T' => 'Teacher (ครู)',
        'ADM' => 'Administrator'
    ];
    return isset($map[$role_edoc]) ? $map[$role_edoc] : $role_edoc;
}

// สร้างเมนูตาม role
function getMenuByRole($role) {
    $menus = [
        'ครู' => [
            ['label' => 'หน้าหลัก', 'href' => 'dashboard.php'],
            ['label' => 'ดูเอกสาร', 'href' => 'view-documents.php'],
        ],
        'เจ้าหน้าที่' => [
            ['label' => 'หน้าหลัก', 'href' => 'dashboard.php'],
            ['label' => 'ดูเอกสาร', 'href' => 'view-documents.php'],
            ['label' => 'อัปโหลด', 'href' => 'upload-documents.php'],
        ],
        'ผู้บริหาร' => [
            ['label' => 'หน้าหลัก', 'href' => 'dashboard.php'],
            ['label' => 'ดูเอกสาร', 'href' => 'view-documents.php'],
        ],
        'admin' => [
            ['label' => 'หน้าหลัก', 'href' => 'dashboard.php'],
            ['label' => 'ดูเอกสาร', 'href' => 'view-documents.php'],
            ['label' => 'อัปโหลด', 'href' => 'upload-documents.php'],
            ['label' => 'ตั้งค่า', 'href' => 'settings.php'],
            ['label' => 'จัดการผู้ใช้', 'href' => 'manage-users.php'],
            ['label' => 'รายงาน', 'href' => 'reports.php'],
        ],
    ];
    // เพิ่มออกจากระบบให้ทุก role
    $logout = ['label' => 'ออกจากระบบ', 'href' => 'logout.php', 'class' => 'text-red-300'];
    $roleMenus = isset($menus[$role]) ? $menus[$role] : [];
    $roleMenus[] = $logout;
    return $roleMenus;
}

// สร้างลิงก์รูปโปรไฟล์
$ImgProfileUser = '';
if (!empty($user['Teach_photo'])) {
    $ImgProfileUser = 'https://std.phichai.ac.th/teacher/uploads/phototeach/' . $user['Teach_photo'];
} else {
    $ImgProfileUser = 'logo-phicha.png'; // fallback รูป default
}

$roleWelcome = [
    'ครู' => '👩‍🏫 ยินดีต้อนรับคุณครู',
    'เจ้าหน้าที่' => '🧑‍💼 ยินดีต้อนรับเจ้าหน้าที่',
    'ผู้บริหาร' => '👨‍💼 ยินดีต้อนรับผู้บริหาร',
    'admin' => '🛡️ Welcome, Administrator'
];

$menuItems = getMenuByRole($role);

// โหลด autoload ถ้ายังไม่มี
require_once __DIR__ . '/classes/DatabaseDocuments.php';

use App\DatabaseDocuments;

// ดึงจำนวนเอกสารแต่ละ group_type
$dbDoc = new DatabaseDocuments();
$groupCounts = [];
$totalCount = 0;
$groupMap = [
    1 => ['label' => 'วิชาการ', 'emoji' => '📚', 'color' => 'indigo'],
    2 => ['label' => 'บุคคล', 'emoji' => '👥', 'color' => 'pink'],
    3 => ['label' => 'กิจการนักเรียน', 'emoji' => '🎒', 'color' => 'green'],
    4 => ['label' => 'การเงิน', 'emoji' => '💰', 'color' => 'red'],
    5 => ['label' => 'บริหารทั่วไป', 'emoji' => '🏢', 'color' => 'yellow'],
];

$sql = "SELECT group_type, COUNT(*) as cnt FROM uploads GROUP BY group_type";
foreach ($dbDoc->fetchAll($sql) as $row) {
    $groupCounts[$row['group_type']] = $row['cnt'];
    $totalCount += $row['cnt'];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <?php require_once 'header.php'; ?>
</head>
<body class="bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-blue-700 to-purple-700 shadow text-white px-4 md:px-6 py-4 flex flex-row md:justify-between md:items-center gap-3 md:gap-0 relative">
        <div class="flex items-center space-x-3">
            <?php if (!empty($pageConfig['logoLink'])): ?>
                <img src="<?php echo htmlspecialchars($pageConfig['logoLink']); ?>" alt="logo" class="h-10 w-10 rounded-full bg-white p-1 shadow" />
            <?php else: ?>
                <span class="text-2xl">📄</span>
            <?php endif; ?>
            <span class="font-bold text-xl tracking-wide"><?php echo htmlspecialchars($pageConfig['nameschool']); ?></span>
        </div>
        <ul class="hidden md:flex flex-wrap justify-center md:justify-start space-x-4 md:space-x-6 text-base">
            <?php foreach ($menuItems as $item): ?>
                <li>
                    <a href="<?php echo htmlspecialchars($item['href']); ?>" class="hover:underline <?php echo isset($item['class']) ? $item['class'] : ''; ?>">
                        <?php echo htmlspecialchars($item['label']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="flex items-center space-x-2 justify-center md:justify-end mt-2 md:mt-0">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-gradient-to-tr from-blue-300 to-purple-300 flex items-center justify-center text-xl md:text-2xl shadow overflow-hidden">
                <img src="<?php echo htmlspecialchars($ImgProfileUser); ?>" alt="profile" class="object-cover w-full h-full">
            </div>
            <div class="flex flex-col">
                <span class="font-semibold text-sm md:text-base">
                    <?php echo htmlspecialchars(isset($user['Teach_name']) ? $user['Teach_name'] : 'ไม่ทราบชื่อ'); ?>
                </span>
                <span class="text-xs text-blue-100">
                    <?php echo htmlspecialchars($role); ?> | 
                    <?php echo htmlspecialchars(getRoleEdocName(isset($user['role_edoc']) ? $user['role_edoc'] : '')); ?>
                </span>
            </div>
        </div>
        <!-- Floating Mobile Menu Button (top-right) -->
        <div x-data="{ open: false }" class="md:hidden absolute top-4 right-4">
            <button @click="open = !open"
                class="bg-gradient-to-r from-blue-700 to-purple-700 text-white rounded-full shadow-lg w-14 h-14 flex items-center justify-center focus:outline-none transition hover:scale-110"
                aria-label="เมนู">
                <svg x-show="!open" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <!-- Mobile Menu Drawer -->
            <div x-show="open" @click.away="open = false"
                class="absolute right-0 mt-2 bg-gradient-to-r from-blue-700 to-purple-700 rounded-xl shadow-lg p-4 w-48 flex flex-col space-y-3 transition z-50"
                x-transition>
                <?php foreach ($menuItems as $item): ?>
                    <a href="<?php echo htmlspecialchars($item['href']); ?>" class="block text-white hover:underline <?php echo isset($item['class']) ? $item['class'] : ''; ?>">
                        <?php echo htmlspecialchars($item['label']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>
    <!-- Main Content -->
    <main class="flex-1 flex flex-col items-center justify-center py-6 md:py-10 px-2">
        <!-- Dashboard Cards/Graphs with animation -->
        <div class="w-full max-w-4xl grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
            <!-- รวมทั้งหมด -->
            <div class="bg-white rounded-xl shadow p-5 md:p-6 flex flex-col items-center transform transition duration-500 hover:scale-105 hover:shadow-2xl hover:bg-blue-50">
                <span class="text-3xl md:text-4xl mb-2 animate-bounce">📄</span>
                <div class="text-xl md:text-2xl font-bold text-blue-700">
                    <?= $totalCount ?>
                </div>
                <div class="text-gray-600 mt-1 text-sm md:text-base text-center">เอกสารทั้งหมด</div>
            </div>
            <!-- แสดงแต่ละ group_type -->
            <?php foreach ($groupMap as $type => $info): ?>
            <div class="bg-white rounded-xl shadow p-5 md:p-6 flex flex-col items-center transform transition duration-500 hover:scale-105 hover:shadow-2xl hover:bg-<?= $info['color'] ?>-50">
                <span class="text-3xl md:text-4xl mb-2 animate-bounce"><?= $info['emoji'] ?></span>
                <div class="text-xl md:text-2xl font-bold text-<?= $info['color'] ?>-600">
                    <?= isset($groupCounts[$type]) ? $groupCounts[$type] : 0 ?>
                </div>
                <div class="text-gray-600 mt-1 text-sm md:text-base text-center">เอกสารฝ่าย<?= $info['label'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <!-- สามารถเพิ่มกราฟหรือข้อมูลอื่นๆ ได้ที่นี่ -->
    </main>

    <!-- Footer -->
    <?php require_once 'footer.php'; ?>
</body>
</html>
