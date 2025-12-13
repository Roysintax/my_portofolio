<?php
/**
 * Education History Section Component
 * Horizontal Timeline at top, 16:9 cards with content below image
 * Auto-play carousel every 2 minutes
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
            <!-- Horizontal Timeline at Top -->
            <div class="edu-timeline-container">
                <!-- Timeline Line -->
                <div class="edu-timeline-line-wrapper">
                    <div class="edu-timeline-line"></div>
                    <div class="edu-timeline-dots">
                        <?php foreach ($educationHistory as $index => $edu): ?>
                            <?php 
                            $startDate = !empty($edu['start_date']) ? date('M Y', strtotime($edu['start_date'])) : '';
                            $endDate = '';
                            if (!empty($edu['still_studying']) && $edu['still_studying'] == 1) {
                                $endDate = 'Present';
                            } elseif (!empty($edu['end_date'])) {
                                $endDate = date('M Y', strtotime($edu['end_date']));
                            }
                            $isActive = !empty($edu['still_studying']) && $edu['still_studying'] == 1;
                            ?>
                            <div class="edu-dot-wrapper">
                                <span class="edu-dot-date"><?php echo $startDate; ?><?php if($endDate): ?> - <?php echo $endDate; ?><?php endif; ?></span>
                                <div class="edu-dot <?php echo $isActive ? 'active' : ''; ?>">
                                    <?php if ($isActive): ?><span class="pulse"></span><?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Cards - Large 16:9 with content below -->
                <div class="edu-cards-container">
                    <?php foreach ($educationHistory as $index => $edu): ?>
                        <?php 
                        $startDate = !empty($edu['start_date']) ? date('M Y', strtotime($edu['start_date'])) : '';
                        $endDate = '';
                        if (!empty($edu['still_studying']) && $edu['still_studying'] == 1) {
                            $endDate = 'Present';
                        } elseif (!empty($edu['end_date'])) {
                            $endDate = date('M Y', strtotime($edu['end_date']));
                        }
                        
                        $images = [];
                        if (!empty($edu['image_activity'])) {
                            $images = array_filter(array_map('trim', explode(',', $edu['image_activity'])));
                        }
                        $carouselId = 'edu-carousel-' . $index;
                        $isActive = !empty($edu['still_studying']) && $edu['still_studying'] == 1;
                        ?>
                        <div class="edu-card">
                            <!-- Image Area 16:9 -->
                            <div class="edu-card-image">
                                <?php if (!empty($images)): ?>
                                    <?php if (count($images) > 1): ?>
                                        <div class="edu-carousel" id="<?php echo $carouselId; ?>" data-total="<?php echo count($images); ?>">
                                            <?php foreach ($images as $imgIndex => $image): ?>
                                                <div class="edu-slide <?php echo $imgIndex === 0 ? 'active' : ''; ?>">
                                                    <img src="/portofolio/assets/images/<?php echo htmlspecialchars($image); ?>" alt="Activity">
                                                </div>
                                            <?php endforeach; ?>
                                            <button class="edu-nav prev" onclick="eduSlide('<?php echo $carouselId; ?>', -1)">‹</button>
                                            <button class="edu-nav next" onclick="eduSlide('<?php echo $carouselId; ?>', 1)">›</button>
                                            <div class="edu-counter"><span class="current">1</span>/<?php echo count($images); ?></div>
                                            <div class="edu-dots">
                                                <?php foreach ($images as $imgIndex => $image): ?>
                                                    <button class="dot <?php echo $imgIndex === 0 ? 'active' : ''; ?>" onclick="eduGoto('<?php echo $carouselId; ?>', <?php echo $imgIndex; ?>)"></button>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <img src="/portofolio/assets/images/<?php echo htmlspecialchars($images[0]); ?>" alt="Activity">
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="edu-placeholder"><span>📚</span></div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Content Below Image -->
                            <div class="edu-card-content">
                                <div class="edu-card-header">
                                    <div class="edu-date-badge">
                                        <?php echo $startDate . ($endDate ? ' - ' . $endDate : ''); ?>
                                    </div>
                                    <?php if ($isActive): ?>
                                        <span class="edu-active-badge">🟢 Masih Belajar</span>
                                    <?php endif; ?>
                                </div>
                                <h4 class="edu-card-title"><?php echo htmlspecialchars($edu['name_education'] ?? 'Education'); ?></h4>
                                <?php if (!empty($edu['description'])): ?>
                                    <p class="edu-card-desc"><?php echo nl2br(htmlspecialchars($edu['description'])); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state" style="padding: 2rem;">
                <p>No education history added yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.edu-timeline-container { padding: 2rem 0; }

/* Timeline at Top */
.edu-timeline-line-wrapper {
    position: relative;
    padding: 0 3rem;
    margin-bottom: 3rem;
}

.edu-timeline-line {
    height: 5px;
    background: linear-gradient(90deg, var(--accent), #764ba2, #667eea, var(--accent));
    background-size: 200% 100%;
    animation: gradientMove 5s ease infinite;
    border-radius: 3px;
}

@keyframes gradientMove {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.edu-timeline-dots {
    display: flex;
    justify-content: space-between;
    position: absolute;
    top: 0;
    left: 3rem;
    right: 3rem;
    height: 100%;
}

.edu-dot-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    position: relative;
}

.edu-dot-date {
    position: absolute;
    top: -35px;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--accent);
    white-space: nowrap;
    text-align: center;
}

.edu-dot {
    width: 18px;
    height: 18px;
    min-width: 18px;
    min-height: 18px;
    background: var(--accent);
    border-radius: 50%;
    border: 3px solid var(--bg-primary);
    box-shadow: 0 0 0 2px var(--accent);
    position: relative;
    aspect-ratio: 1 / 1;
}

.edu-dot.active {
    background: #4caf50;
    box-shadow: 0 0 0 2px #4caf50;
}

.edu-dot .pulse {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 100%; height: 100%;
    border-radius: 50%;
    background: #4caf50;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    50% { transform: translate(-50%, -50%) scale(2.5); opacity: 0; }
}

/* Cards Container */
.edu-cards-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2.5rem;
}

/* Card */
.edu-card {
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 50px rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.1);
    transition: all 0.3s ease;
}

.edu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 60px rgba(233, 69, 96, 0.2);
    border-color: var(--accent);
}

/* Image Area 16:9 */
.edu-card-image {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* 16:9 */
    overflow: hidden;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
}

.edu-card-image > img {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.edu-placeholder {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 5rem;
    opacity: 0.3;
}

/* Carousel */
.edu-carousel {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
}

.edu-slide {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: all 0.6s ease;
}

.edu-slide.active {
    opacity: 1;
    z-index: 2;
}

.edu-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.edu-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.95);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.8rem;
    z-index: 10;
    opacity: 0;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
}

.edu-card:hover .edu-nav { opacity: 1; }
.edu-nav:hover { background: var(--accent); color: white; transform: translateY(-50%) scale(1.1); }
.edu-nav.prev { left: 15px; }
.edu-nav.next { right: 15px; }

.edu-counter {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    z-index: 10;
}

.edu-dots {
    position: absolute;
    bottom: 15px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 8px;
    z-index: 10;
}

.edu-dots .dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    border: none;
    cursor: pointer;
    padding: 0;
    transition: all 0.3s;
}

.edu-dots .dot.active {
    background: var(--accent);
    width: 28px;
    border-radius: 5px;
}

/* Content Below Image */
.edu-card-content {
    padding: 1.5rem 2rem 2rem;
    background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
}

.edu-card-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.75rem;
    flex-wrap: wrap;
}

.edu-date-badge {
    display: inline-block;
    background: var(--gradient-accent);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 700;
}

.edu-active-badge {
    font-size: 0.85rem;
    color: #4caf50;
    font-weight: 600;
}

.edu-card-title {
    margin: 0 0 0.75rem;
    font-size: 1.4rem;
    font-weight: 700;
    color: #1a1a2e;
}

.edu-card-desc {
    margin: 0;
    font-size: 1rem;
    color: #333333;
    line-height: 1.6;
}

/* Responsive */
@media (max-width: 992px) {
    .edu-cards-container {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .edu-timeline-line-wrapper {
        display: none;
    }
    .edu-card-content {
        padding: 1.25rem 1.5rem 1.5rem;
    }
}
</style>

<script>
// Auto-play interval (2 minutes = 120000ms)
const AUTOPLAY_INTERVAL = 120000;

function eduSlide(carouselId, direction) {
    const carousel = document.getElementById(carouselId);
    if (!carousel) return;
    
    const slides = carousel.querySelectorAll('.edu-slide');
    const dots = carousel.querySelectorAll('.dot');
    const counter = carousel.querySelector('.current');
    const total = parseInt(carousel.dataset.total);
    
    let currentIndex = Array.from(slides).findIndex(s => s.classList.contains('active'));
    slides.forEach(slide => slide.classList.remove('active'));
    
    let newIndex = currentIndex + direction;
    if (newIndex >= total) newIndex = 0;
    if (newIndex < 0) newIndex = total - 1;
    
    slides[newIndex].classList.add('active');
    dots.forEach((d, i) => d.classList.toggle('active', i === newIndex));
    if (counter) counter.textContent = newIndex + 1;
}

function eduGoto(carouselId, targetIndex) {
    const carousel = document.getElementById(carouselId);
    if (!carousel) return;
    
    const slides = carousel.querySelectorAll('.edu-slide');
    const dots = carousel.querySelectorAll('.dot');
    const counter = carousel.querySelector('.current');
    
    slides.forEach(slide => slide.classList.remove('active'));
    slides[targetIndex].classList.add('active');
    dots.forEach((d, i) => d.classList.toggle('active', i === targetIndex));
    if (counter) counter.textContent = targetIndex + 1;
}

// Auto-play for all carousels
document.addEventListener('DOMContentLoaded', function() {
    const carousels = document.querySelectorAll('.edu-carousel');
    
    carousels.forEach(carousel => {
        const carouselId = carousel.id;
        const total = parseInt(carousel.dataset.total);
        
        if (total > 1) {
            // Start auto-play
            setInterval(() => {
                eduSlide(carouselId, 1);
            }, AUTOPLAY_INTERVAL);
        }
    });
});
</script>
