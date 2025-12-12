<?php
/**
 * Admin - Education & Certification Management
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Manage Education & Certs';

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM education_and_certification_page WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: index.php?deleted=1');
    exit;
}

$items = $pdo->query("SELECT * FROM education_and_certification_page ORDER BY id DESC")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="data-card">
    <div class="data-card-header">
        <h3>All Education & Certifications</h3>
        <a href="add.php" class="btn btn-add btn-sm">+ Add New</a>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Education Name</th>
                <th>Certificate Name</th>
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
                        <td><?php echo htmlspecialchars($item['category'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($item['name_education_history'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($item['name_certificate'] ?? '-'); ?></td>
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
