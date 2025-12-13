<?php
/**
 * Admin - Skills Management
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Manage Skills';

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: index.php?deleted=1');
    exit;
}

$items = $pdo->query("SELECT * FROM skills ORDER BY skill_category, display_order ASC")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<style>
.skill-level-bar {
    width: 100px;
    height: 8px;
    background: #e0e0e0;
    border-radius: 4px;
    overflow: hidden;
}
.skill-level-fill {
    height: 100%;
    background: var(--gradient-accent);
    border-radius: 4px;
}
.skill-icon-preview {
    width: 40px;
    height: 40px;
    object-fit: contain;
}
.category-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: rgba(102, 126, 234, 0.2);
    color: #667eea;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}
</style>

<div class="data-card">
    <div class="data-card-header">
        <h3>⚡ Skills</h3>
        <div style="display: flex; gap: 0.5rem;">
            <a href="export.php" class="btn btn-sm" style="background: #27ae60; color: white;">📥 Download Excel</a>
            <a href="add.php" class="btn btn-add btn-sm">+ Add New</a>
        </div>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Icon</th>
                <th>Skill Name</th>
                <th>Category</th>
                <th>Level</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="5" style="text-align: center;">No skills found</td></tr>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?php if (!empty($item['skill_icon'])): ?>
                                <img src="/portofolio/assets/images/<?php echo htmlspecialchars($item['skill_icon']); ?>" class="skill-icon-preview" alt="Icon">
                            <?php else: ?>
                                <span style="font-size: 1.5rem;">⚡</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo htmlspecialchars($item['skill_name'] ?? '-'); ?></strong></td>
                        <td><span class="category-badge"><?php echo htmlspecialchars($item['skill_category'] ?? '-'); ?></span></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div class="skill-level-bar">
                                    <div class="skill-level-fill" style="width: <?php echo $item['skill_level'] ?? 0; ?>%;"></div>
                                </div>
                                <span style="font-size: 0.8rem;"><?php echo $item['skill_level'] ?? 0; ?>%</span>
                            </div>
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-edit btn-sm">Edit</a>
                            <a href="index.php?delete=<?php echo $item['id']; ?>" class="btn btn-delete btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
