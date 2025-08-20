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
    $year = (int)$term->pee;
    
    // Support both activity ID and activity name
    $activity_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $activity_name = isset($_GET['activity']) ? trim($_GET['activity']) : '';
    
    if ($activity_id) {
        // Search by ID
        $stmt = $pdo->prepare('SELECT bm.student_id, bm.created_at FROM best_members bm WHERE bm.year = :y AND bm.activity_id = :a ORDER BY bm.created_at');
        $stmt->execute(['y'=>$year, 'a'=>$activity_id]);
    } elseif ($activity_name) {
        // Search by name
        $stmt = $pdo->prepare('SELECT bm.student_id, bm.created_at FROM best_members bm 
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
