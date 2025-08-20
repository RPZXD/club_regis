<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode(['ok' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__.'/../../classes/DatabaseClub.php';
require_once __DIR__.'/../../models/TermPee.php';

use App\DatabaseClub;

try {
    $club = new DatabaseClub();
    $pdo = $club->getPDO();
    $term = \TermPee::getCurrent();
    $year = (int)$term->pee;

    $stmt = $pdo->prepare('SELECT 
        ba.name AS activity_name,
        "" AS teacher,
        COUNT(bm.id) AS cnt
    FROM best_activities ba 
    LEFT JOIN best_members bm ON bm.activity_id = ba.id AND bm.year = :year
    WHERE ba.year = :year
    GROUP BY ba.id, ba.name
    ORDER BY cnt DESC, ba.name');
    
    $stmt->execute(['year' => $year]);
    $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    echo json_encode(['ok' => true, 'data' => $data]);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
