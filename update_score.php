<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$data = json_decode(file_get_contents('php://input'), true);
$isCorrect = $data['isCorrect'] ?? false;

if (updateUserScore($db, $_SESSION['user_id'], $isCorrect)) {
    http_response_code(200);
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false]);
} 