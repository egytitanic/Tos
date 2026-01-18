<?php
session_start();
header('Content-Type: application/json');
require_once '../core/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $sql = "
        SELECT
            a.id,
            a.name,
            a.icon,
            a.content,
            a.type,
            COUNT(au.app_id) as usage_count
        FROM app_usage au
        JOIN apps a ON au.app_id = a.id
        WHERE au.user_id = ?
        GROUP BY au.app_id
        ORDER BY usage_count DESC
        LIMIT 5
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $most_used_apps = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($most_used_apps);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
