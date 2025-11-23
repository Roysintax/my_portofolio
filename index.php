<?php
require_once 'config/database.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Portfolio</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap" rel="stylesheet">
    <!-- Three.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div id="canvas-container"></div>

    <!-- Navbar -->
    <nav class="navbar">
        <a href="?page=dashboard" class="<?= $page == 'dashboard' ? 'active' : '' ?>">Dashboard</a>
        <a href="?page=project" class="<?= $page == 'project' ? 'active' : '' ?>">Projects</a>
        <a href="?page=design" class="<?= $page == 'design' ? 'active' : '' ?>">Designs</a>
        <a href="?page=education" class="<?= $page == 'education' ? 'active' : '' ?>">Education</a>
        <a href="?page=experience" class="<?= $page == 'experience' ? 'active' : '' ?>">Experience</a>
        <a href="?page=admin" class="<?= $page == 'admin' ? 'active' : '' ?>">Admin</a>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <?php
        $module_file = "modules/$page/view.php";
        if (file_exists($module_file)) {
            include $module_file;
        } else {
            echo "<div class='glass-card text-center'><h2>404 - Page Not Found</h2><p>The requested module does not exist.</p></div>";
        }
        ?>
    </div>

    <!-- Custom JS -->
    <script src="assets/js/3d_animation.js"></script>
    <script src="assets/js/file_upload.js"></script>
</body>
</html>
