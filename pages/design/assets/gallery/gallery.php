<?php
/**
 * Design Gallery Section Component
 * Supports multiple images per design (carousel)
 */
?>

<!-- Design Gallery -->
<section class="section">
    <div class="container">
        <?php if (!empty($designs)): ?>
            <div class="design-grid">
                <?php foreach ($designs as $index => $design): ?>
                    <?php 
                    // Parse images (comma-separated)
                    $images = [];
                    if (!empty($design['design_image'])) {
                        $images = array_filter(array_map('trim', explode(',', $design['design_image'])));
                    }
                    $carouselId = 'design-carousel-' . $index;
                    $hasMultiple = count($images) > 1;
                    ?>
                    <div class="design-card">
                        <?php if (!empty($images)): ?>
                            <?php if ($hasMultiple): ?>
                                <!-- Carousel for multiple images -->
                                <div class="design-carousel" id="<?php echo $carouselId; ?>" data-total="<?php echo count($images); ?>">
                                    <div class="design-carousel-viewport">
                                        <?php foreach ($images as $imgIndex => $image): ?>
                                            <div class="design-slide <?php echo $imgIndex === 0 ? 'active' : ''; ?>" data-index="<?php echo $imgIndex; ?>">
                                                <img src="/portofolio/assets/images/<?php echo htmlspecialchars($image); ?>" alt="Design">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <!-- Navigation -->
                                    <button class="design-nav prev" onclick="designSlide('<?php echo $carouselId; ?>', -1)">‹</button>
                                    <button class="design-nav next" onclick="designSlide('<?php echo $carouselId; ?>', 1)">›</button>
                                    
                                    <!-- Counter -->
                                    <div class="design-counter">
                                        <span class="current">1</span>/<?php echo count($images); ?>
                                    </div>
                                    
                                    <!-- Indicators -->
                                    <div class="design-indicators">
                                        <?php foreach ($images as $imgIndex => $image): ?>
                                            <button class="dot <?php echo $imgIndex === 0 ? 'active' : ''; ?>" 
                                                    onclick="designGoto('<?php echo $carouselId; ?>', <?php echo $imgIndex; ?>)"></button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Single image -->
                                <img src="/portofolio/assets/images/<?php echo htmlspecialchars($images[0]); ?>" alt="Design">
                            <?php endif; ?>
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.5);">
                                No Image
                            </div>
                        <?php endif; ?>
                        
                        <div class="design-overlay">
                            <?php if (!empty($design['title'])): ?>
                                <h4><?php echo htmlspecialchars($design['title']); ?></h4>
                            <?php endif; ?>
                            <?php if (!empty($design['design_link'])): ?>
                                <a href="<?php echo htmlspecialchars($design['design_link']); ?>" target="_blank">
                                    View Design &#8599;
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🎨</div>
                <h3>No Designs Yet</h3>
                <p>Design portfolio will be displayed here once added to the database.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* Design Carousel Styles */
.design-carousel {
    position: relative;
    width: 100%;
    height: 100%;
}

.design-carousel-viewport {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.design-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transform: scale(1.05);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.design-slide.active {
    opacity: 1;
    transform: scale(1);
    z-index: 2;
}

.design-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Navigation Buttons */
.design-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    cursor: pointer;
    font-size: 1.2rem;
    color: #333;
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
}

.design-card:hover .design-nav {
    opacity: 1;
}

.design-nav:hover {
    background: var(--accent);
    color: white;
}

.design-nav.prev { left: 8px; }
.design-nav.next { right: 8px; }

/* Counter */
.design-counter {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    z-index: 10;
}

/* Indicators */
.design-indicators {
    position: absolute;
    bottom: 50px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 4px;
    z-index: 10;
}

.design-indicators .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    border: none;
    cursor: pointer;
    padding: 0;
    transition: all 0.3s ease;
}

.design-indicators .dot.active {
    background: var(--accent);
    width: 16px;
    border-radius: 3px;
}

/* Design overlay with title */
.design-overlay h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    color: white;
}
</style>

<script>
// Design Carousel Functions
function designSlide(carouselId, direction) {
    const carousel = document.getElementById(carouselId);
    if (!carousel) return;
    
    const slides = carousel.querySelectorAll('.design-slide');
    const dots = carousel.querySelectorAll('.dot');
    const counter = carousel.querySelector('.current');
    const total = parseInt(carousel.dataset.total);
    
    let currentIndex = Array.from(slides).findIndex(s => s.classList.contains('active'));
    
    slides.forEach(slide => slide.classList.remove('active'));
    
    let newIndex = currentIndex + direction;
    if (newIndex >= total) newIndex = 0;
    if (newIndex < 0) newIndex = total - 1;
    
    slides[newIndex].classList.add('active');
    dots.forEach((dot, i) => dot.classList.toggle('active', i === newIndex));
    if (counter) counter.textContent = newIndex + 1;
}

function designGoto(carouselId, targetIndex) {
    const carousel = document.getElementById(carouselId);
    if (!carousel) return;
    
    const slides = carousel.querySelectorAll('.design-slide');
    const dots = carousel.querySelectorAll('.dot');
    const counter = carousel.querySelector('.current');
    
    slides.forEach(slide => slide.classList.remove('active'));
    slides[targetIndex].classList.add('active');
    dots.forEach((dot, i) => dot.classList.toggle('active', i === targetIndex));
    if (counter) counter.textContent = targetIndex + 1;
}
</script>
