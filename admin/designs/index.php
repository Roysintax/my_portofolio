<?php
/**
 * Admin - Designs Management
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Manage Designs';

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM design_page WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: index.php?deleted=1');
    exit;
}

$designs = $pdo->query("SELECT * FROM design_page ORDER BY id DESC")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<style>
.thumbnail-container {
    position: relative;
    display: inline-block;
}
.thumbnail-container img {
    width: 80px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}
.image-count-badge {
    position: absolute;
    bottom: -5px;
    right: -5px;
    background: var(--accent);
    color: white;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
</style>

<div class="data-card">
    <div class="data-card-header">
        <h3>All Designs</h3>
        <div style="display: flex; gap: 0.5rem;">
            <a href="export.php" class="btn btn-sm" style="background: #27ae60; color: white;">📥 Download Excel</a>
            <a href="add.php" class="btn btn-add btn-sm">+ Add New</a>
        </div>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Preview</th>
                <th>Link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($designs)): ?>
                <tr><td colspan="5" style="text-align: center;">No designs found</td></tr>
            <?php else: ?>
                <?php foreach ($designs as $design): ?>
                    <?php 
                    // Parse images and get first one for thumbnail
                    $images = [];
                    if (!empty($design['design_image'])) {
                        $images = array_filter(array_map('trim', explode(',', $design['design_image'])));
                    }
                    $firstImage = !empty($images) ? $images[0] : null;
                    $imageCount = count($images);
                    ?>
                    <tr>
                        <td><?php echo $design['id']; ?></td>
                        <td><?php echo htmlspecialchars($design['title'] ?? 'Untitled'); ?></td>
                        <td>
                            <?php if ($firstImage): ?>
                                <div class="thumbnail-container">
                                    <img src="/portofolio/assets/images/<?php echo htmlspecialchars($firstImage); ?>" alt="Design">
                                    <?php if ($imageCount > 1): ?>
                                        <span class="image-count-badge">+<?php echo $imageCount - 1; ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span style="color: #999;">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($design['design_link']): ?>
                                <a href="<?php echo htmlspecialchars($design['design_link']); ?>" target="_blank">View ↗</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $design['id']; ?>" class="btn btn-edit btn-sm">Edit</a>
                            <a href="index.php?delete=<?php echo $design['id']; ?>" class="btn btn-delete btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
