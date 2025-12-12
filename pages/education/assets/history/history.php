<?php
/**
 * Education History Section Component
 * Supports multiple images per education (carousel)
 */
?>

<!-- Education History -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Education History</h2>
            <div class="line"></div>
        </div>
        
        <?php if (!empty($educationHistory)): ?>
            <div class="grid grid-2">
                <?php foreach ($educationHistory as $index => $edu): ?>
                    <div class="education-card">
                        <?php if (!empty($edu['category'])): ?>
                            <span class="text-accent" style="font-size: 0.85rem; font-weight: 600;">
                                <?php echo htmlspecialchars($edu['category']); ?>
                            </span>
                        <?php endif; ?>
                        
                        <h4><?php echo htmlspecialchars($edu['name_education_history']); ?></h4>
                        
                        <?php if (!empty($edu['image_activity'])): ?>
                            <?php 
                            // Parse images (comma-separated or single)
                            $images = explode(',', $edu['image_activity']);
                            $images = array_filter(array_map('trim', $images));
                            $carouselId = 'edu-carousel-' . $index;
                            ?>
                            
                            <?php if (count($images) > 1): ?>
                                <!-- Multiple Images Carousel -->
                                <div class="edu-carousel" id="<?php echo $carouselId; ?>" data-total="<?php echo count($images); ?>">
                                    <div class="edu-carousel-viewport">
                                        <?php foreach ($images as $imgIndex => $image): ?>
                                            <div class="edu-carousel-slide <?php echo $imgIndex === 0 ? 'active' : ''; ?>" data-index="<?php echo $imgIndex; ?>">
                                                <img src="/portofolio/assets/images/<?php echo htmlspecialchars($image); ?>" alt="Activity <?php echo $imgIndex + 1; ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <!-- Controls -->
                                    <button class="edu-nav-btn edu-prev" onclick="eduSlide('<?php echo $carouselId; ?>', -1)">‹</button>
                                    <button class="edu-nav-btn edu-next" onclick="eduSlide('<?php echo $carouselId; ?>', 1)">›</button>
                                    
                                    <!-- Counter -->
                                    <div class="edu-counter">
                                        <span class="edu-current">1</span> / <?php echo count($images); ?>
                                    </div>
                                    
                                    <!-- Indicators -->
                                    <div class="edu-indicators">
                                        <?php foreach ($images as $imgIndex => $image): ?>
                                            <button class="edu-dot <?php echo $imgIndex === 0 ? 'active' : ''; ?>" 
                                                    onclick="eduGoto('<?php echo $carouselId; ?>', <?php echo $imgIndex; ?>)"></button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Single Image -->
                                <div class="certificate-image">
                                    <img src="/portofolio/assets/images/<?php echo htmlspecialchars($images[0]); ?>" alt="Activity">
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state" style="padding: 2rem;">
                <p>No education history added yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* Education Carousel - 16:9 Aspect Ratio */
.edu-carousel {
    position: relative;
    width: 100%;
    margin-top: 1rem;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    background: #f0f0f0;
}

.edu-carousel-viewport {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* 16:9 */
}

.edu-carousel-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transform: scale(1.05) translateX(50px);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
}

.edu-carousel-slide.active {
    opacity: 1;
    transform: scale(1) translateX(0);
    pointer-events: auto;
    z-index: 2;
}

.edu-carousel-slide.prev {
    opacity: 0;
    transform: scale(0.95) translateX(-50px);
}

.edu-carousel-slide.next {
    opacity: 0;
    transform: scale(0.95) translateX(50px);
}

.edu-carousel-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Navigation Buttons */
.edu-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    cursor: pointer;
    font-size: 1.5rem;
    color: var(--primary);
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    z-index: 10;
    opacity: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.edu-carousel:hover .edu-nav-btn {
    opacity: 1;
}

.edu-nav-btn:hover {
    background: var(--accent);
    color: white;
    transform: translateY(-50%) scale(1.1);
}

.edu-prev { left: 10px; }
.edu-next { right: 10px; }

/* Counter */
.edu-counter {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    z-index: 10;
}

/* Indicators */
.edu-indicators {
    position: absolute;
    bottom: 12px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 6px;
    z-index: 10;
}

.edu-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    border: none;
    cursor: pointer;
    padding: 0;
    transition: all 0.3s ease;
}

.edu-dot:hover {
    background: rgba(255, 255, 255, 0.8);
}

.edu-dot.active {
    background: var(--accent);
    width: 20px;
    border-radius: 4px;
}
</style>

<script>
// Education Carousel Functions
function eduSlide(carouselId, direction) {
    const carousel = document.getElementById(carouselId);
    if (!carousel) return;
    
    const slides = carousel.querySelectorAll('.edu-carousel-slide');
    const dots = carousel.querySelectorAll('.edu-dot');
    const counter = carousel.querySelector('.edu-current');
    const total = parseInt(carousel.dataset.total);
    
    let currentIndex = Array.from(slides).findIndex(s => s.classList.contains('active'));
    
    slides.forEach(slide => slide.classList.remove('active', 'prev', 'next'));
    slides[currentIndex].classList.add(direction > 0 ? 'prev' : 'next');
    
    let newIndex = currentIndex + direction;
    if (newIndex >= total) newIndex = 0;
    if (newIndex < 0) newIndex = total - 1;
    
    setTimeout(() => slides[newIndex].classList.add('active'), 50);
    
    dots.forEach((dot, i) => dot.classList.toggle('active', i === newIndex));
    
    if (counter) {
        counter.style.transform = 'scale(1.2)';
        counter.textContent = newIndex + 1;
        setTimeout(() => counter.style.transform = 'scale(1)', 150);
    }
}

function eduGoto(carouselId, targetIndex) {
    const carousel = document.getElementById(carouselId);
    if (!carousel) return;
    
    const slides = carousel.querySelectorAll('.edu-carousel-slide');
    const dots = carousel.querySelectorAll('.edu-dot');
    const counter = carousel.querySelector('.edu-current');
    
    let currentIndex = Array.from(slides).findIndex(s => s.classList.contains('active'));
    if (currentIndex === targetIndex) return;
    
    const direction = targetIndex > currentIndex ? 1 : -1;
    
    slides.forEach(slide => slide.classList.remove('active', 'prev', 'next'));
    slides[currentIndex].classList.add(direction > 0 ? 'prev' : 'next');
    
    setTimeout(() => slides[targetIndex].classList.add('active'), 50);
    
    dots.forEach((dot, i) => dot.classList.toggle('active', i === targetIndex));
    
    if (counter) {
        counter.textContent = targetIndex + 1;
    }
}
</script>
