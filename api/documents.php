<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../classes/DatabaseDocuments.php';
require_once __DIR__ . '/../models/document.php';

use App\Models\Document;

$docModel = new Document();
$data = $docModel->getAll();

echo json_encode([
    'data' => $data
]);
