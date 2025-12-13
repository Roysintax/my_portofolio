<?php
/**
 * Education Page
 * Display education history and certifications from separate tables
 */

$pageTitle = 'Education';

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

// Fetch education history from new table
try {
    $stmt = $pdo->query("SELECT * FROM education_history ORDER BY start_date DESC");
    $educationHistory = $stmt->fetchAll();
} catch (PDOException $e) {
    $educationHistory = [];
}

// Fetch certifications from new table
try {
    $stmt = $pdo->query("SELECT * FROM certifications ORDER BY issue_date DESC");
    $certifications = $stmt->fetchAll();
} catch (PDOException $e) {
    $certifications = [];
}

// Include header
include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/header.php';

// Include education components
include __DIR__ . '/assets/hero/hero.php';
include __DIR__ . '/assets/history/history.php';
include __DIR__ . '/assets/certifications/certifications.php';

// Include footer
include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/footer.php';
?>
