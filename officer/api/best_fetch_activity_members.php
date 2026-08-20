<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode(['ok'=>false,'message'=>'Unauthorized','data'=>[]]);
    exit;
}

    require_once __DIR__.'/../../classes/DatabaseClub.php';
    require_once __DIR__.'/../../classes/DatabaseUsers.php';
    require_once __DIR__.'/../../models/TermPee.php';

    use App\DatabaseClub;
    use App\DatabaseUsers;
    
try {


    $club = new DatabaseClub();
    $pdo = $club->getPDO();
    $users = new DatabaseUsers();
    $term = \TermPee::getCurrent();
    $currentYear = $term ? (int)$term->pee : (int)date('Y') + 543;
    $year = isset($_GET['year']) && (int)$_GET['year'] > 2000 ? (int)$_GET['year'] : $currentYear;
    
    // Support both activity ID and activity name
    $activity_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $activity_name = isset($_GET['activity']) ? trim($_GET['activity']) : '';
    
    $actInfo = null;
    if ($activity_id) {
        // Fetch activity info
        $actStmt = $pdo->prepare('SELECT id, name, grade_levels, max_members, year FROM best_activities WHERE id = ?');
        $actStmt->execute([$activity_id]);
        $actInfo = $actStmt->fetch(PDO::FETCH_ASSOC);
        if ($actInfo && isset($actInfo['year'])) {
            $year = (int)$actInfo['year'];
        }

        // Search by ID
        $stmt = $pdo->prepare('SELECT bm.student_id, bm.created_at FROM best_members bm WHERE bm.year = :y AND bm.activity_id = :a ORDER BY bm.created_at');
        $stmt->execute(['y'=>$year, 'a'=>$activity_id]);
    } elseif ($activity_name) {
        // Search by name
        $stmt = $pdo->prepare('SELECT bm.student_id, bm.created_at, ba.name, ba.grade_levels, ba.max_members, ba.year FROM best_members bm 
                              INNER JOIN best_activities ba ON ba.id = bm.activity_id 
                              WHERE bm.year = :y AND ba.name = :name ORDER BY bm.created_at');
        $stmt->execute(['y'=>$year, 'name'=>$activity_name]);
    } else {
        echo json_encode(['ok'=>false,'message'=>'Missing activity parameter','data'=>[]]);
        exit;
    }

    $out = [];
    while ($m = $stmt->fetch()) {
        $stu = $users->getStudentByUsername($m['student_id']);
        $out[] = [
            'student_id' => $m['student_id'],
            'name' => $stu ? ($stu['Stu_pre'].$stu['Stu_name'].' '.$stu['Stu_sur']) : $m['student_id'],
            'room' => $stu ? ('ม.'.$stu['Stu_major'].'/'.$stu['Stu_room']) : '',
            'created_at' => $m['created_at']
        ];
    }

    echo json_encode(['ok'=>true,'data'=>$out]);
} catch (Exception $e) {
    echo json_encode(['ok'=>false,'message'=>'Database error: ' . $e->getMessage(),'data'=>[]]);
}
?>
