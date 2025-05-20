<?php
require_once 'conexao.php';
session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = isset($data['task_id']) ? (int)$data['task_id'] : 0;

if ($id > 0) {
    try {
        $stmt = $conn->prepare("DELETE FROM task WHERE task_id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
