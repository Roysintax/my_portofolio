<?php
/**
 * Home Hero Section Component
 */
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Welcome to My <span class="highlight">Portfolio</span></h1>
                <p>I am a passionate developer and designer creating beautiful digital experiences. Explore my work, projects, and professional journey.</p>
                <div class="hero-buttons">
                    <a href="/portofolio/pages/project/" class="btn btn-primary">View Projects</a>
                    <a href="/portofolio/pages/design/" class="btn btn-outline">See Designs</a>
                </div>
            </div>
            
            <div class="hero-image">
                <?php if ($dashboard && !empty($dashboard['photo_profil'])): ?>
                    <img src="/portofolio/assets/images/<?php echo htmlspecialchars($dashboard['photo_profil']); ?>" alt="Profile Photo">
                <?php else: ?>
                    <div class="placeholder-image" style="width: 100%; height: 400px; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem;">
                        Profile Image
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
