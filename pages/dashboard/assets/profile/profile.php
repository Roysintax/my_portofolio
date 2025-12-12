<?php
/**
 * Dashboard Profile Section Component
 * Displays profile image and about me content
 */
?>

<!-- Profile Section -->
<section class="section">
    <div class="container">
        <?php if (!empty($dashboardItems)): ?>
            <?php $profile = $dashboardItems[0]; ?>
            <div class="profile-section">
                <div class="profile-image">
                    <?php if (!empty($profile['photo_profil'])): ?>
                        <img src="/portofolio/assets/images/<?php echo htmlspecialchars($profile['photo_profil']); ?>" alt="Profile Photo">
                    <?php else: ?>
                        <div class="placeholder-image" style="width: 100%; height: 300px; background: linear-gradient(135deg, #e94560 0%, #ff6b6b 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: #fff;">
                            Photo
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="profile-content">
                    <h2>About Me</h2>
                    <p class="animate-text"><?php echo nl2br(htmlspecialchars($profile['carrousel_teks'] ?? 'Welcome to my portfolio dashboard.')); ?></p>
                    
                    <div style="margin-top: 2rem;">
                        <a href="/portofolio/pages/project/" class="btn btn-primary">View My Projects</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No profile data available yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
