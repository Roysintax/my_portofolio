<?php
/**
 * Admin - Profile/Dashboard Management
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Profile/Dashboard';

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM dashboard WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: index.php?deleted=1');
    exit;
}

$items = $pdo->query("SELECT * FROM dashboard ORDER BY id DESC")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="data-card">
    <div class="data-card-header">
        <h3>Profile/Dashboard Items</h3>
        <a href="add.php" class="btn btn-add btn-sm">+ Add New</a>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Profile Photo</th>
                <th>Carousel Image</th>
                <th>Text</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="5" style="text-align: center;">No items found</td></tr>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td>
                            <?php if ($item['photo_profil']): ?>
                                <img src="/portofolio/assets/images/<?php echo htmlspecialchars($item['photo_profil']); ?>" alt="Profile">
                            <?php else: ?>
                                <span style="color: #999;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($item['carrousel_image']): ?>
                                <img src="/portofolio/assets/images/<?php echo htmlspecialchars($item['carrousel_image']); ?>" alt="Carousel">
                            <?php else: ?>
                                <span style="color: #999;">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars(substr($item['carrousel_teks'] ?? '', 0, 40)) . '...'; ?></td>
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
