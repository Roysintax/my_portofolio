<?php
// Fetch Dashboard Data
$stmt = $pdo->query("SELECT * FROM dashboard");
$dashboard_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Dummy data if empty
if (empty($dashboard_data)) {
    $dashboard_data = [
        [
            'carrousel_image' => 'https://via.placeholder.com/1200x600/00d2ff/ffffff?text=Welcome+to+My+Portfolio',
            'carrousel_teks' => 'Welcome to my creative space.',
            'photo_profil' => 'https://via.placeholder.com/200/ffffff/000000?text=Profile'
        ]
    ];
}

$profile_photo = $dashboard_data[0]['photo_profil'];
?>

<div class="grid-2" style="align-items: center; margin-bottom: 50px;">
    <div class="animate-fade-in">
        <h1 style="font-size: 3rem; margin-bottom: 10px;">Hello, I'm <span style="color: var(--accent-color);">Roy</span></h1>
        <h2 style="font-weight: 300; opacity: 0.9;">Web Developer & UI/UX Enthusiast</h2>
        <p style="line-height: 1.6; opacity: 0.8; margin-top: 20px;">
            I build interactive and beautiful web experiences. Welcome to my personal portfolio dashboard where I showcase my projects, designs, and journey.
        </p>
        <div style="margin-top: 30px;">
            <a href="?page=project" class="btn" style="background: var(--accent-color); color: #000; padding: 12px 30px; border-radius: 30px; text-decoration: none; font-weight: bold; display: inline-block; transition: transform 0.3s;">View My Work</a>
        </div>
    </div>
    <div class="text-center animate-fade-in">
        <div style="position: relative; display: inline-block;">
            <!-- Blue Shadow Effect -->
            <div style="position: absolute; inset: 0; border-radius: 50%; box-shadow: 0 0 30px 10px rgba(0, 210, 255, 0.6); z-index: -1;"></div>
            <img src="<?= htmlspecialchars($profile_photo) ?>" alt="Profile" style="width: 280px; height: 280px; border-radius: 50%; border: 4px solid rgba(255,255,255,0.2); object-fit: cover; box-shadow: 0 0 20px rgba(0, 210, 255, 0.8);">
        </div>
</div>

<script>
    // Simple Carousel Script
    let slideIndex = 0;
    const slides = document.querySelectorAll('.carousel-item');
    
    if (slides.length > 1) {
        setInterval(() => {
            slides[slideIndex].style.display = 'none';
            slideIndex = (slideIndex + 1) % slides.length;
            slides[slideIndex].style.display = 'block';
        }, 4000);
    }
</script>
