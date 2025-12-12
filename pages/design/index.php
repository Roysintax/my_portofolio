<?php
/**
 * Design Page
 * Display design portfolio gallery
 * Components are separated into individual folders in assets/
 */

$pageTitle = 'Design';

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

// Fetch design data
try {
    $stmt = $pdo->query("SELECT * FROM design_page ORDER BY id DESC");
    $designs = $stmt->fetchAll();
} catch (PDOException $e) {
    $designs = [];
}

// Include header
include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/header.php';

// Include design components
include __DIR__ . '/assets/hero/hero.php';
include __DIR__ . '/assets/gallery/gallery.php';

// Include footer
include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/footer.php';
?>
