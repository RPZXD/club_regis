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

// ดึง Stu_major (ระดับชั้น) ของนักเรียน
require_once('../config/Database.php');
$db = (new Database('phichaia_student'))->getConnection();
$stu_id = $_SESSION['user'];
$stmt = $db->prepare("SELECT Stu_major, Stu_name, Stu_sur FROM student WHERE Stu_id = :stu_id LIMIT 1");
$stmt->bindParam(':stu_id', $stu_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$student_grade = $row ? $row['Stu_major'] : '';
$student_name = $row ? $row['Stu_name'] : '';
$student_surname = $row ? $row['Stu_sur'] : '';
$student_grade_text = '';
if ($student_grade !== '') {
    $student_grade_text = 'ม.' . intval($student_grade);
}

// ดึง club_id ที่สมัครแล้วใน term/pee ปัจจุบัน (ใช้ฐาน phichaia_club)
require_once('../models/TermPee.php');
$termPeeModel = new TermPeeModel();
$termpee = $termPeeModel->getTermPee();
$term = $termpee['term'];
$pee = $termpee['pee'];
$db_club = (new Database('phichaia_club'))->getConnection();
$stmt = $db_club->prepare(
    "SELECT c.club_name, c.description, c.advisor_teacher, c.grade_levels, c.term, c.year
     FROM club_members m
     INNER JOIN clubs c ON m.club_id = c.club_id
     WHERE m.student_id = :stu_id AND c.term = :term AND c.year = :pee
     LIMIT 1"
);
$stmt->bindParam(':stu_id', $stu_id);
$stmt->bindParam(':term', $term);
$stmt->bindParam(':pee', $pee);
$stmt->execute();
$club = $stmt->fetch(PDO::FETCH_ASSOC);

// ดึงชื่อครูที่ปรึกษาชุมนุม (Teach_name) จาก Teach_id
$advisor_name = '';
if ($club && !empty($club['advisor_teacher'])) {
    $db_teacher = (new Database('phichaia_student'))->getConnection();
    $stmt_teacher = $db_teacher->prepare("SELECT Teach_name FROM teacher WHERE Teach_id = :teach_id LIMIT 1");
    $stmt_teacher->bindParam(':teach_id', $club['advisor_teacher']);
    $stmt_teacher->execute();
    $row_teacher = $stmt_teacher->fetch(PDO::FETCH_ASSOC);
    $advisor_name = $row_teacher ? $row_teacher['Teach_name'] : '';
}
?>

<body class="bg-gray-100 min-h-screen">
<div class="wrapper">
    <?php require_once('../includes/wrapper.php'); ?>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h5 class="m-0 text-2xl font-bold text-blue-700 flex items-center gap-2">
                            <span>🎉</span> ชุมนุมของฉัน
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container mx-auto max-w-2xl py-6">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <p class="text-gray-700 mb-4 text-lg flex items-center gap-2">
                        <span>📋</span> ข้อมูลชุมนุมที่คุณสมัครในเทอมนี้
                    </p>
                    <div class="mb-6 bg-blue-50 rounded p-4 flex flex-col gap-1">
                        <div><span class="font-semibold text-blue-800">🙍‍♂️ ชื่อ-สกุล:</span> <span class="text-gray-800"><?php echo htmlspecialchars($student_name . ' ' . $student_surname); ?></span></div>
                        <div><span class="font-semibold text-blue-800">🆔 รหัสนักเรียน:</span> <span class="text-gray-800"><?php echo htmlspecialchars($stu_id); ?></span></div>
                        <div><span class="font-semibold text-blue-800">🏫 ระดับชั้น:</span> <span class="text-gray-800"><?php echo htmlspecialchars($student_grade_text); ?></span></div>
                    </div>
                    <?php if ($club): ?>
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden shadow">
                            <tbody>
                                <tr class="border-b">
                                    <th class="py-2 px-4 bg-blue-100 text-blue-900 text-left w-1/3">🏷️ ชื่อชุมนุม</th>
                                    <td class="py-2 px-4"><?php echo htmlspecialchars($club['club_name']); ?></td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 px-4 bg-blue-100 text-blue-900 text-left">📝 รายละเอียด</th>
                                    <td class="py-2 px-4"><?php echo nl2br(htmlspecialchars($club['description'])); ?></td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 px-4 bg-blue-100 text-blue-900 text-left">👨‍🏫 ครูที่ปรึกษาชุมนุม</th>
                                    <td class="py-2 px-4"><?php echo htmlspecialchars($advisor_name); ?></td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 px-4 bg-blue-100 text-blue-900 text-left">🎓 ระดับชั้นที่เปิด</th>
                                    <td class="py-2 px-4"><?php echo htmlspecialchars($club['grade_levels']); ?></td>
                                </tr>
                                <tr>
                                    <th class="py-2 px-4 bg-blue-100 text-blue-900 text-left">📅 เทอม/ปี</th>
                                    <td class="py-2 px-4"><?php echo htmlspecialchars($club['term']) . ' / ' . htmlspecialchars($club['year']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="mt-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 rounded flex items-center gap-2">
                            <span>⚠️</span> คุณยังไม่ได้สมัครชุมนุมในเทอมนี้
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
    <?php require_once('../includes/footer.php'); ?>
</div>
<?php require_once('../includes/script.php'); ?>
</body>
</html>
