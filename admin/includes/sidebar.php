<?php
/**
 * Admin Sidebar Component
 */

// Determine current page for active state
$currentAdminPage = basename(dirname($_SERVER['PHP_SELF']));
if ($currentAdminPage === 'admin') {
    $currentAdminPage = 'dashboard';
}
?>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <h2>Admin<span>Panel</span></h2>
    </div>
    
    <nav class="sidebar-menu">
        <div class="menu-label">Main</div>
        <a href="/portofolio/admin/dashboard/" class="menu-item <?php echo $currentAdminPage === 'dashboard' ? 'active' : ''; ?>">
            <span class="icon">📊</span>
            <span>Dashboard</span>
        </a>
        
        <div class="menu-label">Content Management</div>
        <a href="/portofolio/admin/home_carousel/" class="menu-item <?php echo $currentAdminPage === 'home_carousel' ? 'active' : ''; ?>">
            <span class="icon">🏠</span>
            <span>Home Carousel</span>
        </a>
        <a href="/portofolio/admin/projects/" class="menu-item <?php echo $currentAdminPage === 'projects' ? 'active' : ''; ?>">
            <span class="icon">💻</span>
            <span>Projects</span>
        </a>
        <a href="/portofolio/admin/designs/" class="menu-item <?php echo $currentAdminPage === 'designs' ? 'active' : ''; ?>">
            <span class="icon">🎨</span>
            <span>Designs</span>
        </a>
        <a href="/portofolio/admin/education_history/" class="menu-item <?php echo $currentAdminPage === 'education_history' ? 'active' : ''; ?>">
            <span class="icon">📚</span>
            <span>Education History</span>
        </a>
        <a href="/portofolio/admin/certifications/" class="menu-item <?php echo $currentAdminPage === 'certifications' ? 'active' : ''; ?>">
            <span class="icon">🏆</span>
            <span>Certifications</span>
        </a>
        <a href="/portofolio/admin/skills/" class="menu-item <?php echo $currentAdminPage === 'skills' ? 'active' : ''; ?>">
            <span class="icon">⚡</span>
            <span>Skills</span>
        </a>
        <a href="/portofolio/admin/experience/" class="menu-item <?php echo $currentAdminPage === 'experience' ? 'active' : ''; ?>">
            <span class="icon">💼</span>
            <span>Work Experience</span>
        </a>
        <a href="/portofolio/admin/profile/" class="menu-item <?php echo $currentAdminPage === 'profile' ? 'active' : ''; ?>">
            <span class="icon">👤</span>
            <span>Profile/Dashboard</span>
        </a>
        
        <div class="menu-label">Account</div>
        <a href="/portofolio/admin/logout.php" class="menu-item">
            <span class="icon">🚪</span>
            <span>Logout</span>
        </a>
    </nav>
</aside>
