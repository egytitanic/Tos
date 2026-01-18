<?php
session_start();
require_once 'core/db.php';

// --- Security & Validation ---

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access Denied. Please log in to run apps.");
}

// 2. Check if an app ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request. No application specified.");
}

$userId = $_SESSION['user_id'];
$appId = (int)$_GET['id'];

try {
    // --- Authorization & App Fetching ---

    // 1. Verify the user has installed this app
    $stmt = $pdo->prepare(
        "SELECT a.id, a.name, a.type, a.content, a.price_monthly
         FROM apps a
         JOIN user_apps ua ON a.id = ua.app_id
         WHERE ua.user_id = ? AND ua.app_id = ? AND a.is_active = 1"
    );
    $stmt->execute([$userId, $appId]);
    $app = $stmt->fetch();

    if (!$app) {
        die("App not found or you do not have permission to run it. Please install it from the App Store first.");
    }

    // 2. TODO: Subscription Check for paid apps
    // if ($app['price_monthly'] > 0) {
    //     // Check for an active subscription in the 'subscriptions' table
    //     // If no active subscription, die("This is a paid app. Please subscribe first.");
    // }

    // --- Record App Usage ---
    // This helps populate the "Most Used" list in the Start Menu
    $usageStmt = $pdo->prepare("INSERT INTO app_usage (user_id, app_id) VALUES (?, ?)");
    $usageStmt->execute([$userId, $appId]);


    // --- Run the App ---

    // For iframe apps, redirect to the content URL
    if ($app['type'] === 'iframe') {
        header("Location: " . $app['content']);
        exit();
    }

    // For html apps, render the content directly
    if ($app['type'] === 'html') {
        echo $app['content'];
        exit();
    }

    // Fallback for unknown app types
    die("Unsupported application type.");

} catch (PDOException $e) {
    // In production, log this error instead of showing it to the user.
    die("Database error. Could not run the application.");
}
