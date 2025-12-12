<?php
/**
 * Work Experience Timeline Section Component
 * Supports multiple images per experience (carousel with 16:9 aspect ratio)
 * Enhanced with start/end dates, still working status, and work type
 */
?>

<!-- Experience Timeline -->
<section class="section">
    <div class="container">
        <?php if (!empty($experiences)): ?>
            <div class="timeline">
                <?php foreach ($experiences as $index => $exp): ?>
                    <div class="timeline-item">
                        <div class="timeline-date">
                            <?php 
                            // Format date range
                            $startDate = '';
                            $endDate = '';
                            
                            if (!empty($exp['date_work_start'])) {
                                $startDate = date('M Y', strtotime($exp['date_work_start']));
                            }
                            
                            if (!empty($exp['still_working']) && $exp['still_working'] == 1) {
                                $endDate = 'Present';
                            } elseif (!empty($exp['date_work_end'])) {
                                $endDate = date('M Y', strtotime($exp['date_work_end']));
                            }
                            
                            if ($startDate && $endDate) {
                                echo $startDate . ' - ' . $endDate;
                            } elseif ($startDate) {
                                echo $startDate;
                            } else {
                                echo 'Date not specified';
                            }
                            ?>
                            
                            <?php if (!empty($exp['still_working']) && $exp['still_working'] == 1): ?>
                                <?php if ($exp['work_status'] === 'magang'): ?>
                                    <span class="still-working-badge magang">🟢 Masih Magang</span>
                                <?php else: ?>
                                    <span class="still-working-badge kerja">🟢 Masih Bekerja</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="timeline-content">
                            <!-- Work Status Badge -->
                            <?php if (!empty($exp['work_status'])): ?>
                                <span class="work-status-badge <?php echo $exp['work_status']; ?>">
                                    <?php echo $exp['work_status'] === 'magang' ? '📚 Magang' : '💼 Kerja'; ?>
                                </span>
                            <?php endif; ?>
                            
                            <h4><?php echo htmlspecialchars($exp['name_company'] ?? 'Company Name'); ?></h4>
                            <p><?php echo nl2br(htmlspecialchars($exp['activity_description'] ?? '')); ?></p>
                            
                            <?php if (!empty($exp['image_activity_work_carrousel'])): ?>
                                <?php 
                                // Parse images (comma-separated or single)
                                $images = explode(',', $exp['image_activity_work_carrousel']);
                                $images = array_filter(array_map('trim', $images));
                                $carouselId = 'exp-carousel-' . $index;
                                ?>
                                
                                <?php if (count($images) > 1): ?>
                                    <!-- Multiple Images Carousel (16:9) -->
                                    <div class="exp-carousel" id="<?php echo $carouselId; ?>" data-total="<?php echo count($images); ?>">
                                        <div class="exp-carousel-viewport">
                                            <div class="exp-carousel-track">
                                                <?php foreach ($images as $imgIndex => $image): ?>
                                                    <div class="exp-carousel-slide" data-index="<?php echo $imgIndex; ?>">
                                                        <img src="/portofolio/assets/images/<?php echo htmlspecialchars($image); ?>" alt="Work Activity <?php echo $imgIndex + 1; ?>">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Controls -->
                                        <button class="exp-nav-btn exp-prev" onclick="expSlide('<?php echo $carouselId; ?>', -1)">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="15,18 9,12 15,6"></polyline>
                                            </svg>
                                        </button>
                                        <button class="exp-nav-btn exp-next" onclick="expSlide('<?php echo $carouselId; ?>', 1)">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="9,6 15,12 9,18"></polyline>
                                            </svg>
                                        </button>
                                        
                                        <div class="exp-counter">
                                            <span class="exp-current">1</span> / <?php echo count($images); ?>
                                        </div>
                                        
                                        <div class="exp-indicators">
                                            <?php foreach ($images as $imgIndex => $image): ?>
                                                <button class="exp-dot <?php echo $imgIndex === 0 ? 'active' : ''; ?>" 
                                                        onclick="expGoto('<?php echo $carouselId; ?>', <?php echo $imgIndex; ?>)"></button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- Single Image -->
                                    <div class="timeline-image-single">
                                        <img src="/portofolio/assets/images/<?php echo htmlspecialchars($images[0]); ?>" alt="Work Activity">
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div style="font-size: 4rem; margin-bottom: 1rem;">💼</div>
                <h3>No Work Experience Yet</h3>
                <p>Work experience will be displayed here once added to the database.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* Timeline Content Centering */
.timeline-content {
    text-align: center;
}
.timeline-content h4,
.timeline-content p {
    text-align: center;
}

/* Work Status Badge */
.work-status-badge {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
}
.work-status-badge.kerja {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.work-status-badge.magang {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

/* Still Working Badge */
.still-working-badge {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.8rem;
    font-weight: 600;
}
.still-working-badge.kerja {
    color: #4caf50;
}
.still-working-badge.magang {
    color: #ff9800;
}

/* Experience Carousel - 16:9 Aspect Ratio Horizontal */
.exp-carousel {
    position: relative;
    width: 100%;
    max-width: 800px;
    margin: 1.5rem auto 0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    background: #000;
}

.exp-carousel-viewport {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
    overflow: hidden;
}

.exp-carousel-track {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.exp-carousel-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transform: scale(1.1) translateX(100px);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
}

.exp-carousel-slide.active {
    opacity: 1;
    transform: scale(1) translateX(0);
    pointer-events: auto;
    z-index: 2;
}

.exp-carousel-slide.prev {
    opacity: 0;
    transform: scale(0.9) translateX(-100px);
}

.exp-carousel-slide.next {
    opacity: 0;
    transform: scale(0.9) translateX(100px);
}

.exp-carousel-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Navigation Buttons */
.exp-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.95);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #333;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 10;
    opacity: 0;
}

.exp-carousel:hover .exp-nav-btn {
    opacity: 1;
}

.exp-nav-btn:hover {
    background: var(--accent);
    color: white;
    transform: translateY(-50%) scale(1.1);
}

.exp-prev { left: 12px; }
.exp-next { right: 12px; }

/* Counter Badge */
.exp-counter {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    z-index: 10;
}

/* Indicators */
.exp-indicators {
    position: absolute;
    bottom: 16px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 8px;
    z-index: 10;
}

.exp-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    border: none;
    cursor: pointer;
    padding: 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.exp-dot.active {
    background: var(--accent);
    width: 24px;
    border-radius: 4px;
}

/* Single Image Style */
.timeline-image-single {
    margin: 1.5rem auto 0;
    max-width: 800px;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.timeline-image-single img {
    width: 100%;
    aspect-ratio: 16/9;
    object-fit: cover;
    display: block;
}

/* Progress Bar Animation */
.exp-carousel::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: var(--gradient-accent);
    z-index: 20;
    animation: progressBar 5s linear infinite;
    width: 0%;
}

@keyframes progressBar {
    0% { width: 0%; }
    100% { width: 100%; }
}

@media (max-width: 768px) {
    .exp-carousel { max-width: 100%; border-radius: 12px; }
    .exp-nav-btn { width: 36px; height: 36px; opacity: 1; }
}
</style>

<script>
// Initialize all carousels on page load
document.addEventListener('DOMContentLoaded', function() {
    const carousels = document.querySelectorAll('.exp-carousel');
    
    carousels.forEach(carousel => {
        const slides = carousel.querySelectorAll('.exp-carousel-slide');
        if (slides.length > 0) {
            slides[0].classList.add('active');
        }
        
        // Auto-play
        startAutoPlay(carousel.id);
        
        carousel.addEventListener('mouseenter', () => stopAutoPlay(carousel.id));
        carousel.addEventListener('mouseleave', () => startAutoPlay(carousel.id));
        
        // Touch/Swipe support
        let touchStartX = 0;
        carousel.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; stopAutoPlay(carousel.id); }, { passive: true });
        carousel.addEventListener('touchend', e => {
            const touchEndX = e.changedTouches[0].screenX;
            const diff = touchStartX - touchEndX;
            if (Math.abs(diff) > 50) {
                expSlide(carousel.id, diff > 0 ? 1 : -1);
            }
            startAutoPlay(carousel.id);
        }, { passive: true });
    });
});

const autoPlayIntervals = {};

function startAutoPlay(carouselId) {
    stopAutoPlay(carouselId);
    autoPlayIntervals[carouselId] = setInterval(() => { expSlide(carouselId, 1); }, 5000);
}

function stopAutoPlay(carouselId) {
    if (autoPlayIntervals[carouselId]) clearInterval(autoPlayIntervals[carouselId]);
}

function expSlide(carouselId, direction) {
    const carousel = document.getElementById(carouselId);
    if (!carousel) return;
    
    const slides = carousel.querySelectorAll('.exp-carousel-slide');
    const dots = carousel.querySelectorAll('.exp-dot');
    const counter = carousel.querySelector('.exp-current');
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

function expGoto(carouselId, targetIndex) {
    const carousel = document.getElementById(carouselId);
    if (!carousel) return;
    
    const slides = carousel.querySelectorAll('.exp-carousel-slide');
    const dots = carousel.querySelectorAll('.exp-dot');
    const counter = carousel.querySelector('.exp-current');
    
    let currentIndex = Array.from(slides).findIndex(s => s.classList.contains('active'));
    if (currentIndex === targetIndex) return;
    
    const direction = targetIndex > currentIndex ? 1 : -1;
    
    slides.forEach(slide => slide.classList.remove('active', 'prev', 'next'));
    slides[currentIndex].classList.add(direction > 0 ? 'prev' : 'next');
    
    setTimeout(() => slides[targetIndex].classList.add('active'), 50);
    
    dots.forEach((dot, i) => dot.classList.toggle('active', i === targetIndex));
    
    if (counter) counter.textContent = targetIndex + 1;
    
    stopAutoPlay(carouselId);
    startAutoPlay(carouselId);
}
</script>
