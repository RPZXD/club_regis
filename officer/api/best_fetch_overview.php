<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'เจ้าหน้าที่') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized', 'data' => []]);
    exit;
}

require_once __DIR__.'/../../classes/DatabaseClub.php';
require_once __DIR__.'/../../models/BestActivity.php';
require_once __DIR__.'/../../models/TermPee.php';

use App\DatabaseClub;
use App\Models\BestActivity;

try {
    $db = new DatabaseClub();
    $pdo = $db->getPDO();
    $best = new BestActivity($pdo);
    $term = \TermPee::getCurrent();
    $year = (int)$term->pee;

    $rows = $best->getAll($year);
    foreach ($rows as &$r) {
        $r['current_members_count'] = $best->countMembers((int)$r['id'], $year);
        $r['fill_percent'] = (int)$r['max_members'] > 0 ? round($r['current_members_count']*100/(int)$r['max_members']) : 0;
    }

    echo json_encode(['success' => true, 'data' => $rows, 'year' => $year]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => []]);
}
?>
