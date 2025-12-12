<?php
/**
 * Dashboard Page
 * Profile overview with carousel
 * Components are separated into individual folders in assets/
 */

$pageTitle = 'Dashboard';

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

// Fetch dashboard data
try {
    $stmt = $pdo->query("SELECT * FROM dashboard ORDER BY id DESC");
    $dashboardItems = $stmt->fetchAll();
} catch (PDOException $e) {
    $dashboardItems = [];
}

// Include header
include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/header.php';

// Include dashboard components
include __DIR__ . '/assets/hero/hero.php';
include __DIR__ . '/assets/profile/profile.php';
include __DIR__ . '/assets/carousel/carousel.php';

// Include footer
include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/footer.php';
?>

<!-- Dashboard Specific Scripts -->
<script type="module" src="assets/profile/animation.js"></script>
