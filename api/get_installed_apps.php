<?php
header('Content-Type: application/json');
session_start();
require_once '../core/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'apps' => []]);
    exit();
}

$userId = $_SESSION['user_id'];
$response = ['success' => true, 'apps' => []];

try {
    $stmt = $pdo->prepare(
        "SELECT a.id, a.name, a.icon
         FROM apps a
         JOIN user_apps ua ON a.id = ua.app_id
         WHERE ua.user_id = ?
         ORDER BY a.name ASC"
    );
    $stmt->execute([$userId]);
    $installedApps = $stmt->fetchAll();

    $response['apps'] = $installedApps;

} catch (PDOException $e) {
    $response['success'] = false;
    // In a real app, you would log this error.
}

echo json_encode($response);
