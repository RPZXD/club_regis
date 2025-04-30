<?php
require_once '../models/Club.php';

$action = $_GET['action'] ?? '';

if ($action === 'list') {
    $clubModel = new ClubModel();
    $clubs = $clubModel->getAllClubsWithCurrentMembers();

    // ดึงชื่ออาจารย์ที่ปรึกษา (Teach_name) จาก Teach_id
    require_once '../config/database.php';
    $database = new Database('phichaia_student');
    $db = $database->getConnection();

    foreach ($clubs as &$club) {
        $advisorId = $club['advisor_teacher'];
        $stmt = $db->prepare("SELECT Teach_name FROM teacher WHERE Teach_id = :id LIMIT 1");
        $stmt->bindParam(':id', $advisorId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && isset($row['Teach_name'])) {
            // Remove prefix and prepend "คุณครู"
            $name = $row['Teach_name'];
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
            $club['advisor_teacher_name'] = 'คุณครู' . $name;
        } else {
            $club['advisor_teacher_name'] = '';
        }
    }
    unset($club);

    echo json_encode(['data' => $clubs]);
    exit();
}

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $clubModel = new ClubModel();
    $data = [
        'club_name' => $_POST['club_name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'grade_levels' => isset($_POST['grade_levels']) ? trim($_POST['grade_levels']) : '',
        'max_members' => $_POST['max_members'] ?? '',
        'advisor_teacher' => $_POST['advisor_teacher'] ?? ''
    ];
    // Validate
    if (
        empty($data['club_name']) ||
        empty($data['description']) ||
        empty($data['grade_levels']) ||
        empty($data['max_members']) ||
        empty($data['advisor_teacher'])
    ) {
        echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
        exit();
    }
    $result = $clubModel->createClub($data);
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกข้อมูลได้']);
    }
    exit();
}

// เพิ่ม action สำหรับลบ
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $clubModel = new ClubModel();
    $club_id = $_POST['club_id'] ?? '';
    if (empty($club_id)) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสชมรม']);
        exit();
    }
    $result = $clubModel->deleteClub($club_id);
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบข้อมูลได้']);
    }
    exit();
}

// เพิ่ม action สำหรับแก้ไข
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $clubModel = new ClubModel();
    $data = [
        'club_id' => $_POST['club_id'] ?? '',
        'club_name' => $_POST['club_name'] ?? '',
        'description' => $_POST['description'] ?? '',
        'grade_levels' => isset($_POST['grade_levels']) ? trim($_POST['grade_levels']) : '',
        'max_members' => $_POST['max_members'] ?? ''
    ];
    if (
        empty($data['club_id']) ||
        empty($data['club_name']) ||
        empty($data['description']) ||
        empty($data['grade_levels']) ||
        empty($data['max_members'])
    ) {
        echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
        exit();
    }
    $result = $clubModel->updateClub($data);
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถแก้ไขข้อมูลได้']);
    }
    exit();
}
?>
