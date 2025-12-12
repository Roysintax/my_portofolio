<?php
/**
 * Admin Dashboard - Main Overview
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Dashboard';

// Get counts for stats
try {
    $projectCount = $pdo->query("SELECT COUNT(*) FROM project_page")->fetchColumn();
    $designCount = $pdo->query("SELECT COUNT(*) FROM design_page")->fetchColumn();
    $educationCount = $pdo->query("SELECT COUNT(*) FROM education_and_certification_page")->fetchColumn();
    $experienceCount = $pdo->query("SELECT COUNT(*) FROM work_experience_page")->fetchColumn();
} catch (PDOException $e) {
    $projectCount = $designCount = $educationCount = $experienceCount = 0;
}

include __DIR__ . '/../includes/header.php';
?>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon projects">💻</div>
        <div class="stat-info">
            <h3><?php echo $projectCount; ?></h3>
            <p>Projects</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon designs">🎨</div>
        <div class="stat-info">
            <h3><?php echo $designCount; ?></h3>
            <p>Designs</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon education">🎓</div>
        <div class="stat-info">
            <h3><?php echo $educationCount; ?></h3>
            <p>Education & Certs</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon experience">💼</div>
        <div class="stat-info">
            <h3><?php echo $experienceCount; ?></h3>
            <p>Work Experience</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="data-card">
    <div class="data-card-header">
        <h3>Quick Actions</h3>
    </div>
    <div style="padding: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="/portofolio/admin/projects/add.php" class="btn btn-add">+ Add Project</a>
        <a href="/portofolio/admin/designs/add.php" class="btn btn-add">+ Add Design</a>
        <a href="/portofolio/admin/education/add.php" class="btn btn-add">+ Add Education</a>
        <a href="/portofolio/admin/experience/add.php" class="btn btn-add">+ Add Experience</a>
        <a href="/portofolio/admin/home_carousel/add.php" class="btn btn-add">+ Add Carousel Item</a>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
