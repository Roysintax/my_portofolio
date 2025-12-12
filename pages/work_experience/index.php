<?php
/**
 * Work Experience Page
 * Display professional work history
 * Components are separated into individual folders in assets/
 */

$pageTitle = 'Work Experience';

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

// Fetch work experience data
try {
    $stmt = $pdo->query("SELECT * FROM work_experience_page ORDER BY date_work DESC");
    $experiences = $stmt->fetchAll();
} catch (PDOException $e) {
    $experiences = [];
}

// Include header
include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/header.php';

// Include work experience components
include __DIR__ . '/assets/hero/hero.php';
include __DIR__ . '/assets/timeline/timeline.php';

// Include footer
include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/footer.php';
?>
