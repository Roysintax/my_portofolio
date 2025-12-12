<?php
/**
 * Header Component
 * Contains navbar and head section
 */

// Determine current page for active state
$currentPage = basename(dirname($_SERVER['PHP_SELF']));
if ($currentPage === 'portofolio') {
    $currentPage = 'home';
}

// Base URL for navigation
$baseUrl = '/portofolio';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Professional Portfolio - Showcasing creative work, projects, and experience">
    <meta name="keywords" content="portfolio, web developer, designer, projects">
    <meta name="author" content="Portfolio Owner">
    
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Roysihan Portfolio</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo $baseUrl; ?>/assets/images/favicon.png">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="<?php echo $baseUrl; ?>/" class="navbar-brand">
                Roy<span>sihan</span>
            </a>
            
            <ul class="nav-menu">
                <li>
                    <a href="<?php echo $baseUrl; ?>/pages/home/" class="nav-link <?php echo $currentPage === 'home' ? 'active' : ''; ?>">
                        Home
                    </a>
                </li>
                <li>
                    <a href="<?php echo $baseUrl; ?>/pages/design/" class="nav-link <?php echo $currentPage === 'design' ? 'active' : ''; ?>">
                        Design
                    </a>
                </li>
                <li>
                    <a href="<?php echo $baseUrl; ?>/pages/project/" class="nav-link <?php echo $currentPage === 'project' ? 'active' : ''; ?>">
                        Project
                    </a>
                </li>
                <li>
                    <a href="<?php echo $baseUrl; ?>/pages/work_experience/" class="nav-link <?php echo $currentPage === 'work_experience' ? 'active' : ''; ?>">
                        Work Experience
                    </a>
                </li>
                <li>
                    <a href="<?php echo $baseUrl; ?>/pages/education/" class="nav-link <?php echo $currentPage === 'education' ? 'active' : ''; ?>">
                        Education
                    </a>
                </li>
            </ul>
            
            <div class="nav-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>
