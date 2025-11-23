<?php
$stmt = $pdo->query("SELECT * FROM education_and_certification_page");
$education_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="glass-card">
    <h2>Education & Certifications</h2>
    <p>My academic journey and professional achievements.</p>
</div>

<div class="glass-card" style="position: relative; overflow: hidden; padding: 0; background: transparent; box-shadow: none;">
    <?php if (empty($education_data)): ?>
        <div class="text-center" style="padding: 50px; background: rgba(255,255,255,0.05); border-radius: 15px;">
            <p>No education or certifications found.</p>
        </div>
    <?php else: ?>
        <div class="premium-carousel" id="education-carousel">
            <?php foreach ($education_data as $index => $item): 
                // Handle JSON arrays
                $activity_images = json_decode($item['image_activity'], true);
                if (!is_array($activity_images)) $activity_images = [$item['image_activity']];
                
                $cert_images = json_decode($item['image_certificate'], true);
                if (!is_array($cert_images)) $cert_images = [$item['image_certificate']];
                
                $main_image = !empty($activity_images[0]) ? $activity_images[0] : (!empty($cert_images[0]) ? $cert_images[0] : 'https://via.placeholder.com/1920x700/00d2ff/ffffff?text=No+Image');
            ?>
                <div class="premium-slide <?= $index === 0 ? 'active' : '' ?>">
                    
                    <!-- Background Image with Zoom Effect -->
                    <div class="slide-image-container">
                        <img src="<?= htmlspecialchars($main_image) ?>" alt="Education Image">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, transparent 0%, rgba(2, 12, 27, 0.8) 60%, #020c1b 100%);"></div>
                    </div>

                    <!-- Floating Content Card -->
                    <div class="slide-content-card">
                        <div style="margin-bottom: 20px;">
                            <span style="background: var(--accent-color); color: #000; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; box-shadow: 0 0 15px rgba(0, 210, 255, 0.4);">
                                <?= htmlspecialchars($item['category']) ?>
                            </span>
                        </div>

                        <h2 class="slide-title"><?= htmlspecialchars($item['name_education_history'] ?: $item['name_certificate']) ?></h2>
                        
                        <div class="slide-text">
                            A significant milestone in my professional journey.
                        </div>
                        
                        <?php if (!empty($item['link_certificate'])): ?>
                            <a href="<?= htmlspecialchars($item['link_certificate']) ?>" target="_blank" class="slide-btn">
                                View Certificate
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Controls -->
            <div class="carousel-nav prev" onclick="moveEduSlide(-1)">&#10094;</div>
            <div class="carousel-nav next" onclick="moveEduSlide(1)">&#10095;</div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carouselId = 'education-carousel';
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

        function moveEduSlide(n) {
            clearInterval(autoPlayTimer);
            showSlides(slideIndex + n);
            startAutoPlay();
        }

        function startAutoPlay() {
            if (slides.length > 1) {
                clearInterval(autoPlayTimer);
                autoPlayTimer = setInterval(() => {
                    moveEduSlide(1);
                }, intervalTime);
            }
        }

        if (slides.length > 0) {
            showSlides(0);
            startAutoPlay();
        }

        window.moveEduSlide = moveEduSlide;
    });
</script>
