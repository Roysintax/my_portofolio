<?php
$stmt = $pdo->query("SELECT * FROM work_experience_page ORDER BY date_work DESC");
$experience_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="glass-card">
    <h2>Work Experience</h2>
    <p>My professional career path.</p>
</div>

<div class="glass-card" style="position: relative; overflow: hidden; padding: 0; background: transparent; box-shadow: none;">
    <?php if (empty($experience_data)): ?>
        <div class="text-center" style="padding: 50px; background: rgba(255,255,255,0.05); border-radius: 15px;">
            <p>No work experience found.</p>
        </div>
    <?php else: ?>
        <div class="premium-carousel" id="experience-carousel">
            <?php foreach ($experience_data as $index => $item): 
                // Handle JSON arrays
                $work_images = json_decode($item['image_activity_work_carrousel'], true);
                if (!is_array($work_images)) {
                    $work_images = [$item['image_activity_work_carrousel']];
                }
                $main_image = !empty($work_images[0]) ? $work_images[0] : 'https://via.placeholder.com/1920x700/00d2ff/ffffff?text=No+Image';
            ?>
                <div class="premium-slide <?= $index === 0 ? 'active' : '' ?>">
                    
                    <!-- Background Image with Zoom Effect -->
                    <div class="slide-image-container">
                        <img src="<?= htmlspecialchars($main_image) ?>" alt="Work Image">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, transparent 0%, rgba(2, 12, 27, 0.8) 60%, #020c1b 100%);"></div>
                    </div>

                    <!-- Floating Content Card -->
                    <div class="slide-content-card">
                        <div style="margin-bottom: 20px;">
                            <span style="background: var(--accent-color); color: #000; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; box-shadow: 0 0 15px rgba(0, 210, 255, 0.4);">
                                <?= date('F Y', strtotime($item['date_work'])) ?>
                            </span>
                        </div>

                        <h2 class="slide-title"><?= htmlspecialchars($item['name_company']) ?></h2>
                        
                        <div class="slide-text">
                            <?= nl2br(htmlspecialchars($item['activity_description'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Controls -->
            <div class="carousel-nav prev" onclick="moveExpSlide(-1)">&#10094;</div>
            <div class="carousel-nav next" onclick="moveExpSlide(1)">&#10095;</div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carouselId = 'experience-carousel';
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

        function moveExpSlide(n) {
            clearInterval(autoPlayTimer);
            showSlides(slideIndex + n);
            startAutoPlay();
        }

        function startAutoPlay() {
            if (slides.length > 1) {
                clearInterval(autoPlayTimer);
                autoPlayTimer = setInterval(() => {
                    moveExpSlide(1);
                }, intervalTime);
            }
        }

        if (slides.length > 0) {
            showSlides(0);
            startAutoPlay();
        }

        window.moveExpSlide = moveExpSlide;
    });
</script>
