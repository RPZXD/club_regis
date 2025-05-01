<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'นักเรียน') {
    header('Location: ../login.php');
    exit;
}
$user = $_SESSION['user'];
$stu_id = $user['Stu_id'] ?? '';
$stu_major = isset($user['Stu_major']) ? $user['Stu_major'] : '';
$stu_grade = 'ม.' . $stu_major;

// Read configuration from JSON file
$config = json_decode(file_get_contents('../config.json'), true);
$global = $config['global'];

require_once('../classes/DatabaseClub.php');
require_once('../models/Club.php');
require_once('../classes/DatabaseUsers.php'); // เพิ่มไฟล์นี้

use App\DatabaseClub;
use App\Models\Club;
use App\DatabaseUsers;

$db = new DatabaseClub();
$pdo = $db->getPDO();
$clubModel = new Club($pdo);

// เพิ่มสำหรับดึงชื่อครูที่ปรึกษา
$dbUsers = new DatabaseUsers();

// ดึงชุมนุมที่นักเรียนสมัครไว้
$sql = "SELECT c.*, m.year, m.term, m.created_at 
        FROM club_members m 
        JOIN clubs c ON m.club_id = c.club_id 
        WHERE m.student_id = :stu_id
        ORDER BY m.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['stu_id' => $stu_id]);
$myClubs = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once('header.php');
?>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">
    <?php require_once('wrapper.php');?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0 text-blue-700 font-bold flex items-center gap-2">
                    <span>🌟</span> ชุมนุมของฉัน
                </h1>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <div class="max-w-5xl mx-auto">
                    <?php if (empty($myClubs)): ?>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-6 rounded-lg shadow flex items-center gap-3 mt-10">
                            <span class="text-3xl">😢</span>
                            <span class="text-lg font-semibold">คุณยังไม่ได้สมัครชุมนุมใด ๆ ในปีนี้</span>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                            <?php foreach ($myClubs as $club): ?>
                                <?php
                                    // ดึงชื่อครูที่ปรึกษา
                                    $advisor = $dbUsers->query("SELECT Teach_name FROM teacher WHERE Teach_id = :id", ['id' => $club['advisor_teacher']])->fetch();
                                    $advisor_name = $advisor['Teach_name'] ?? $club['advisor_teacher'];
                                ?>
                                <div class="bg-white rounded-xl shadow-lg border border-blue-200 p-6 flex flex-col gap-3 hover:shadow-2xl transition">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-2xl">🏷️</span>
                                        <div>
                                            <div class="text-xl font-bold text-blue-700"><?= htmlspecialchars($club['club_name']) ?></div>
                                            <div class="text-sm text-gray-500 mt-3">รหัส: <span class="font-mono"><?= htmlspecialchars($club['club_id']) ?></span></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 text-blue-600">
                                        <span>👨‍🏫</span>
                                        <span>ครูที่ปรึกษา: <?= htmlspecialchars($advisor_name) ?></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-blue-500">
                                        <span>🎓</span>
                                        <span>ระดับชั้นที่เปิด: <?= htmlspecialchars($club['grade_levels']) ?></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-700">
                                        <span>📄 รายละเอียดชุมนุม:</span>
                                        <span><?= htmlspecialchars($club['description']) ?></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-green-600">
                                        <span>📅</span>
                                        <span>ปีการศึกษา <?= htmlspecialchars($club['year']) ?> ภาคเรียน <?= htmlspecialchars($club['term']) ?></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-gray-400 text-xs mt-2">
                                        <span>⏰</span>
                                        <span>สมัครเมื่อ <?= htmlspecialchars($club['created_at']) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../footer.php');?>
</div>
<?php require_once('script.php');?>
</body>
</html>
