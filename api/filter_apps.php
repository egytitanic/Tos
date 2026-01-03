<?php
header('Content-Type: application/json');
require_once '../core/db.php';

// Get parameters
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Base query
$sql = "SELECT id, name, description, icon, price_monthly FROM apps WHERE is_active = 1";
$params = [];

// Apply filters
if (!empty($searchTerm)) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
}
if ($category !== 'all') {
    $sql .= " AND category = ?";
    $params[] = $category;
}

// Apply sorting
switch ($sortBy) {
    case 'top_rated':
        $sql .= " ORDER BY rating DESC";
        break;
    case 'price_asc':
        $sql .= " ORDER BY price_monthly ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY price_monthly DESC";
        break;
    case 'newest':
    default:
        $sql .= " ORDER BY created_at DESC";
        break;
}

// Execute query
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $apps = $stmt->fetchAll();
    echo json_encode(['success' => true, 'apps' => $apps]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database query failed.']);
}
