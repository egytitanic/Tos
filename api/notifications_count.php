<?php
session_start();
header('Content-Type: application/json');
require_once '../core/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['unread_count' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $sql = "
        SELECT COUNT(*)
        FROM notifications
        WHERE (user_id = ? OR user_id IS NULL)
          AND is_read = 0
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $unread_count = $stmt->fetchColumn();

    echo json_encode(['unread_count' => $unread_count]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
