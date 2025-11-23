<?php
// Fetch Project Data
$stmt = $pdo->query("SELECT * FROM project_page");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="glass-card">
    <h2>My Projects</h2>
    <p>Here are some of the projects I've worked on.</p>
</div>

<div class="glass-card" style="position: relative; overflow: hidden; padding: 0; background: transparent; box-shadow: none;">
    <?php if (empty($projects)): ?>
        <div class="text-center" style="padding: 50px; background: rgba(255,255,255,0.05); border-radius: 15px;">
            <p>No projects found.</p>
        </div>
    <?php else: ?>
        <div class="premium-carousel">
            <?php foreach ($projects as $index => $project): 
                // Handle JSON array or single path
                $project_images = json_decode($project['project_image'], true);
                if (!is_array($project_images)) {
                    $project_images = [$project['project_image']];
                }
                $main_image = !empty($project_images[0]) ? $project_images[0] : 'https://via.placeholder.com/1920x700/00d2ff/ffffff?text=No+Image';
                
                $tool_logos = json_decode($project['tool_logo'], true);
                if (!is_array($tool_logos)) {
                    $tool_logos = [$project['tool_logo']];
                }
                $main_logo = !empty($tool_logos[0]) ? $tool_logos[0] : '';
            ?>
                <div class="premium-slide <?= $index === 0 ? 'active' : '' ?>">
                    
                    <!-- Background Image with Zoom Effect -->
                    <div class="slide-image-container">
                        <img src="<?= htmlspecialchars($main_image) ?>" alt="Project Image">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, transparent 0%, rgba(2, 12, 27, 0.8) 60%, #020c1b 100%);"></div>
                    </div>

                    <!-- Floating Content Card -->
                    <div class="slide-content-card">
                        <?php if ($main_logo): ?>
                            <img src="<?= htmlspecialchars($main_logo) ?>" alt="Tool" style="width: 50px; height: 50px; object-fit: contain; margin-bottom: 15px; filter: drop-shadow(0 0 10px rgba(255,255,255,0.3));">
                        <?php endif; ?>
                        
                        <h2 class="slide-title"><?= isset($project['project_name']) ? htmlspecialchars($project['project_name']) : 'Project ' . ($index + 1) ?></h2>
                        
                        <div class="slide-text">
                            <?= htmlspecialchars($project['description']) ?>
                        </div>
                        
                        <a href="<?= htmlspecialchars($project['project_link_github']) ?>" target="_blank" class="slide-btn">
                            View Project
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Controls -->
            <div class="carousel-nav prev" onclick="moveProjectSlide(-1)">&#10094;</div>
            <div class="carousel-nav next" onclick="moveProjectSlide(1)">&#10095;</div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let projectSlideIndex = 0;
        const projectSlides = document.querySelectorAll(".premium-slide");
        let projectAutoPlay;
        const intervalTime = 5000;

        function showProjectSlides(n) {
            // Wrap around index
            if (n >= projectSlides.length) { projectSlideIndex = 0; }
            else if (n < 0) { projectSlideIndex = projectSlides.length - 1; }
            else { projectSlideIndex = n; }

            // Remove all active classes
            projectSlides.forEach(slide => {
                slide.classList.remove('active');
            });

            // Add active class to current slide
            if (projectSlides[projectSlideIndex]) {
                projectSlides[projectSlideIndex].classList.add('active');
            }
        }

        function moveProjectSlide(n) {
            clearInterval(projectAutoPlay); // Stop auto-play on interaction
            showProjectSlides(projectSlideIndex + n);
            startProjectAutoPlay(); // Restart auto-play
        }

        function startProjectAutoPlay() {
            if (projectSlides.length > 1) {
                clearInterval(projectAutoPlay);
                projectAutoPlay = setInterval(() => {
                    moveProjectSlide(1);
                }, intervalTime);
            }
        }

        // Initialize
        if (projectSlides.length > 0) {
            showProjectSlides(0);
            startProjectAutoPlay();
        }

        // Make global
        window.moveProjectSlide = moveProjectSlide;
    });
</script>
