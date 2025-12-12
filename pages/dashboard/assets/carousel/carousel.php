<?php
/**
 * Dashboard Carousel Section Component
 * Displays featured gallery carousel
 */
?>

<!-- Dashboard Carousel Section -->
<section class="section" style="padding-top: 0;">
    <div class="container">
        <?php if (!empty($dashboardItems) && count($dashboardItems) > 0): ?>
            <div style="margin-top: 2rem;">
                <h3 style="margin-bottom: 2rem;">Featured Gallery</h3>
                
                <div class="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($dashboardItems as $item): ?>
                            <?php if (!empty($item['carrousel_image'])): ?>
                                <div class="carousel-item">
                                    <img src="/portofolio/assets/images/<?php echo htmlspecialchars($item['carrousel_image']); ?>" alt="Gallery Image">
                                    <?php if (!empty($item['carrousel_teks'])): ?>
                                        <div class="carousel-caption">
                                            <p><?php echo htmlspecialchars($item['carrousel_teks']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($dashboardItems) > 1): ?>
                        <div class="carousel-controls">
                            <button class="carousel-btn carousel-prev">&#10094;</button>
                            <button class="carousel-btn carousel-next">&#10095;</button>
                        </div>
                        
                        <div class="carousel-indicators">
                            <?php foreach ($dashboardItems as $index => $item): ?>
                                <?php if (!empty($item['carrousel_image'])): ?>
                                    <span class="carousel-indicator <?php echo $index === 0 ? 'active' : ''; ?>"></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No gallery content available yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
