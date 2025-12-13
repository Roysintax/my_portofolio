<?php
/**
 * Skills Page
 * Display all skills with progress bars
 */

$pageTitle = 'Skills';

// Include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

// Fetch skills grouped by category
try {
    $stmt = $pdo->query("SELECT * FROM skills ORDER BY skill_category, display_order ASC");
    $allSkills = $stmt->fetchAll();
    
    // Group by category
    $skillsByCategory = [];
    foreach ($allSkills as $skill) {
        $category = $skill['skill_category'] ?: 'Other';
        if (!isset($skillsByCategory[$category])) {
            $skillsByCategory[$category] = [];
        }
        $skillsByCategory[$category][] = $skill;
    }
} catch (PDOException $e) {
    $skillsByCategory = [];
}

// Include header
include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/header.php';
?>

<!-- Skills Hero -->
<section class="section-hero">
    <div class="container">
        <h1>My <span class="text-accent">Skills</span></h1>
        <p>Technologies and tools I work with</p>
    </div>
</section>

<!-- Skills Section -->
<section class="section">
    <div class="container">
        <?php if (!empty($skillsByCategory)): ?>
            <?php foreach ($skillsByCategory as $category => $skills): ?>
                <div class="skill-category">
                    <h3 class="category-title"><?php echo htmlspecialchars($category); ?></h3>
                    <div class="skills-grid">
                        <?php foreach ($skills as $skill): ?>
                            <?php $iconSize = $skill['icon_size'] ?? 50; ?>
                            <div class="skill-card">
                                <?php if (!empty($skill['skill_icon'])): ?>
                                    <img src="/portofolio/assets/images/<?php echo htmlspecialchars($skill['skill_icon']); ?>" 
                                         alt="<?php echo htmlspecialchars($skill['skill_name']); ?>" 
                                         class="skill-icon"
                                         style="width: <?php echo $iconSize; ?>px; height: <?php echo $iconSize; ?>px;">
                                <?php else: ?>
                                    <div class="skill-icon-placeholder" style="width: <?php echo $iconSize; ?>px; height: <?php echo $iconSize; ?>px;">⚡</div>
                                <?php endif; ?>
                                <div class="skill-content">
                                    <h4><?php echo htmlspecialchars($skill['skill_name']); ?></h4>
                                    <div class="skill-progress">
                                        <div class="skill-bar">
                                            <div class="skill-fill" style="--level: <?php echo $skill['skill_level']; ?>%;"></div>
                                        </div>
                                        <span class="skill-percent"><?php echo $skill['skill_level']; ?>%</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <p>No skills added yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.section-hero {
    padding: 6rem 0 3rem;
    text-align: center;
}

.section-hero h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.skill-category {
    margin-bottom: 3rem;
}

.category-title {
    font-size: 1.5rem;
    color: var(--accent);
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid rgba(233, 69, 96, 0.3);
}

.skills-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

.skill-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.03) 100%);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px;
    padding: 1.25rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 1.25rem;
}

.skill-card:hover {
    transform: translateY(-5px);
    border-color: var(--accent);
    box-shadow: 0 10px 30px rgba(233, 69, 96, 0.2);
}

.skill-icon {
    object-fit: contain;
    border-radius: 12px;
    background: rgba(255,255,255,0.1);
    padding: 10px;
    flex-shrink: 0;
}

.skill-icon-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    flex-shrink: 0;
}

.skill-content {
    flex: 1;
    min-width: 0;
}

.skill-content h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    color: #1a1a2e;
}

.skill-progress {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.skill-percent {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--accent);
    flex-shrink: 0;
    min-width: 45px;
}

.skill-bar {
    flex: 1;
    height: 8px;
    background: rgba(255,255,255,0.1);
    border-radius: 4px;
    overflow: hidden;
}

.skill-fill {
    height: 100%;
    width: 0;
    background: var(--gradient-accent);
    border-radius: 5px;
    animation: fillBar 1.5s ease forwards;
}

@keyframes fillBar {
    to {
        width: var(--level);
    }
}
</style>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/portofolio/includes/footer.php'; ?>
