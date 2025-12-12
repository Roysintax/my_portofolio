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

<div class="data-card">
    <div class="data-card-header">
        <h3>All Designs</h3>
        <a href="add.php" class="btn btn-add btn-sm">+ Add New</a>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($designs)): ?>
                <tr><td colspan="4" style="text-align: center;">No designs found</td></tr>
            <?php else: ?>
                <?php foreach ($designs as $design): ?>
                    <tr>
                        <td><?php echo $design['id']; ?></td>
                        <td>
                            <?php if ($design['design_image']): ?>
                                <img src="/portofolio/assets/images/<?php echo htmlspecialchars($design['design_image']); ?>" alt="Design">
                            <?php else: ?>
                                <span style="color: #999;">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($design['design_link']): ?>
                                <a href="<?php echo htmlspecialchars($design['design_link']); ?>" target="_blank">View</a>
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
