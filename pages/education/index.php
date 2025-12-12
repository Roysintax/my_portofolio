<?php
/**
 * Education Page
 * Display education history and certifications
 * Components are separated into individual folders in assets/
 */

$pageTitle = 'Education';

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

// Fetch education data
try {
    $stmt = $pdo->query("SELECT * FROM education_and_certification_page ORDER BY id DESC");
    $educations = $stmt->fetchAll();
} catch (PDOException $e) {
    $educations = [];
}

// Separate by category
$educationHistory = array_filter($educations, function($item) {
    return !empty($item['name_education_history']);
});

$certifications = array_filter($educations, function($item) {
    return !empty($item['name_certificate']);
});

// Include header
include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/header.php';

// Include education components
include __DIR__ . '/assets/hero/hero.php';
include __DIR__ . '/assets/history/history.php';
include __DIR__ . '/assets/certifications/certifications.php';

// Include footer
include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/footer.php';
?>
