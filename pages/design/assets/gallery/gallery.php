<?php
/**
 * Design Gallery Section Component
 */
?>

<!-- Design Gallery -->
<section class="section">
    <div class="container">
        <?php if (!empty($designs)): ?>
            <div class="design-grid">
                <?php foreach ($designs as $design): ?>
                    <div class="design-card">
                        <?php if (!empty($design['design_image'])): ?>
                            <img src="/portofolio/assets/images/<?php echo htmlspecialchars($design['design_image']); ?>" alt="Design">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.5);">
                                No Image
                            </div>
                        <?php endif; ?>
                        
                        <div class="design-overlay">
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
