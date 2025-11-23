<?php
$stmt = $pdo->query("SELECT * FROM design_page");
$designs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="glass-card">
    <h2>Design Portfolio</h2>
    <p>A collection of my creative designs.</p>
</div>

<div class="glass-card" style="position: relative; overflow: hidden; padding: 0; background: transparent; box-shadow: none;">
    <?php if (empty($designs)): ?>
        <div class="text-center" style="padding: 50px; background: rgba(255,255,255,0.05); border-radius: 15px;">
            <p>No designs found.</p>
        </div>
    <?php else: ?>
        <div class="premium-carousel" id="design-carousel">
            <?php foreach ($designs as $index => $design): 
                // Handle JSON array or single path
                $design_images = json_decode($design['design_image'], true);
                if (!is_array($design_images)) {
                    $design_images = [$design['design_image']];
                }
                $main_image = !empty($design_images[0]) ? $design_images[0] : 'https://via.placeholder.com/1920x700/00d2ff/ffffff?text=No+Image';
            ?>
                <div class="premium-slide <?= $index === 0 ? 'active' : '' ?>">
                    
                    <!-- Background Image with Zoom Effect -->
                    <div class="slide-image-container">
                        <img src="<?= htmlspecialchars($main_image) ?>" alt="Design Image">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, transparent 0%, rgba(2, 12, 27, 0.8) 60%, #020c1b 100%);"></div>
                    </div>

                    <!-- Floating Content Card -->
                    <div class="slide-content-card">
                        <h2 class="slide-title">Design Showcase</h2>
                        
                        <div class="slide-text">
                            Explore the details of this design work.
                        </div>
                        
                        <?php if (!empty($design['design_link'])): ?>
                            <a href="<?= htmlspecialchars($design['design_link']) ?>" target="_blank" class="slide-btn">
                                View Full Design
                            </a>
                        <?php else: ?>
                            <span style="opacity: 0.5; display: inline-block; margin-top: 10px;">No link available</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Controls -->
            <div class="carousel-nav prev" onclick="moveDesignSlide(-1)">&#10094;</div>
            <div class="carousel-nav next" onclick="moveDesignSlide(1)">&#10095;</div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carouselId = 'design-carousel';
        const carouselContainer = document.getElementById(carouselId);
        if (!carouselContainer) return;

        let slideIndex = 0;
        const slides = carouselContainer.querySelectorAll(".premium-slide");
        let autoPlayTimer;
        const intervalTime = 5000;

        function showSlides(n) {
            if (n >= slides.length) { slideIndex = 0; }
            else if (n < 0) { slideIndex = slides.length - 1; }
            else { slideIndex = n; }

            slides.forEach(slide => slide.classList.remove('active'));
            if (slides[slideIndex]) {
                slides[slideIndex].classList.add('active');
            }
        }

        function moveDesignSlide(n) {
            clearInterval(autoPlayTimer);
            showSlides(slideIndex + n);
            startAutoPlay();
        }

        function startAutoPlay() {
            if (slides.length > 1) {
                clearInterval(autoPlayTimer);
                autoPlayTimer = setInterval(() => {
                    moveDesignSlide(1);
                }, intervalTime);
            }
        }

        if (slides.length > 0) {
            showSlides(0);
            startAutoPlay();
        }

        window.moveDesignSlide = moveDesignSlide;
    });
</script>

