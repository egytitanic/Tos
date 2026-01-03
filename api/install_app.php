<?php
header('Content-Type: application/json');
session_start();
require_once '../core/db.php';

// Initialize response
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Authentication required. Please log in.';
    echo json_encode($response);
    exit();
}

// Check if app_id is provided
if (!isset($_POST['app_id']) || !is_numeric($_POST['app_id'])) {
    $response['message'] = 'Invalid request: App ID is missing or invalid.';
    echo json_encode($response);
    exit();
}

$userId = $_SESSION['user_id'];
$appId = (int)$_POST['app_id'];

// --- Main Logic ---
try {
    // 1. Check if the app exists and is active
    $stmt = $pdo->prepare("SELECT id FROM apps WHERE id = ? AND is_active = 1");
    $stmt->execute([$appId]);
    if (!$stmt->fetch()) {
        $response['message'] = 'This app does not exist or is not available.';
        echo json_encode($response);
        exit();
    }

    // 2. Check if the user has already installed the app
    $stmt = $pdo->prepare("SELECT id FROM user_apps WHERE user_id = ? AND app_id = ?");
    $stmt->execute([$userId, $appId]);
    if ($stmt->fetch()) {
        $response['message'] = 'You have already installed this app.';
        $response['success'] = true; // Still a "success" from the user's perspective
        echo json_encode($response);
        exit();
    }

    // 3. Insert the installation record
    $stmt = $pdo->prepare("INSERT INTO user_apps (user_id, app_id) VALUES (?, ?)");
    if ($stmt->execute([$userId, $appId])) {
        $response['success'] = true;
        $response['message'] = 'App installed successfully!';
    } else {
        $response['message'] = 'Failed to install the app due to a database error.';
    }

} catch (PDOException $e) {
    // In production, log the error message instead of displaying it.
    $response['message'] = 'Database error: Could not process installation.';
}

echo json_encode($response);
