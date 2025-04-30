<?php
require_once '../config/Database.php';
require_once '../models/TermPee.php';

$action = $_GET['action'] ?? '';

if ($action === 'term') {
    $termPeeModel = new TermPeeModel();
    $termpees = $termPeeModel->getTermPee();
    echo json_encode(['data' => $termpees['term']]);
    exit();
}
if ($action === 'pee') {
    $termPeeModel = new TermPeeModel();
    $termpees = $termPeeModel->getTermPee();
    echo json_encode(['data' => $termpees['pee']]);
    exit();
}