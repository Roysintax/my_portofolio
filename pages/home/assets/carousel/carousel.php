<?php
/**
 * Home Carousel Section Component
 */
?>

<!-- Carousel Section -->
<?php if (!empty($carouselItems)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Featured <span class="text-accent">Work</span></h2>
            <p>A glimpse into my creative journey and accomplishments</p>
            <div class="line"></div>
        </div>
        
        <div class="carousel">
            <div class="carousel-inner">
                <?php foreach ($carouselItems as $item): ?>
                    <div class="carousel-item">
                        <img src="/portofolio/assets/images/<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="carousel-caption">
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p><?php echo htmlspecialchars($item['subtitle']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="carousel-controls">
                <button class="carousel-btn carousel-prev">&#10094;</button>
                <button class="carousel-btn carousel-next">&#10095;</button>
            </div>
            
            <div class="carousel-indicators">
                <?php foreach ($carouselItems as $index => $item): ?>
                    <span class="carousel-indicator <?php echo $index === 0 ? 'active' : ''; ?>"></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
