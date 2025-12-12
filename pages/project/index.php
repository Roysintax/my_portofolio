<?php
/**
 * Project Page
 * Display development projects
 * Components are separated into individual folders in assets/
 */

$pageTitle = 'Projects';

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

// Fetch project data
try {
    $stmt = $pdo->query("SELECT * FROM project_page ORDER BY id DESC");
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $projects = [];
}

// Include header
include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/header.php';

// Include project components
include __DIR__ . '/assets/hero/hero.php';
include __DIR__ . '/assets/grid/grid.php';

// Include footer
include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/footer.php';
?>
