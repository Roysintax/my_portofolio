<?php
/**
 * Home Page
 * Main landing page with carousel
 * Components are separated into individual folders in assets/
 */

$pageTitle = 'Home';

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

// Fetch carousel data
try {
    $stmt = $pdo->query("SELECT * FROM home_carousel ORDER BY id ASC");
    $carouselItems = $stmt->fetchAll();
} catch (PDOException $e) {
    $carouselItems = [];
}

// Fetch dashboard data for profile
try {
    $stmt = $pdo->query("SELECT * FROM dashboard LIMIT 1");
    $dashboard = $stmt->fetch();
} catch (PDOException $e) {
    $dashboard = null;
}

// Include header
include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/header.php';

// Include home components
include __DIR__ . '/assets/hero/hero.php';
include __DIR__ . '/assets/carousel/carousel.php';
include __DIR__ . '/assets/quicklinks/quicklinks.php';

// Include footer
include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/footer.php';
?>
