<?php
/**
 * Admin - Certifications Management
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Certifications';

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM certifications WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: index.php?deleted=1');
    exit;
}

$items = $pdo->query("SELECT * FROM certifications ORDER BY id DESC")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<style>
.cert-thumbnail { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
.pdf-badge { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: #e74c3c; color: white; border-radius: 6px; font-size: 0.7rem; font-weight: 600; }
</style>

<div class="data-card">
    <div class="data-card-header">
        <h3>🏆 Certifications</h3>
        <div style="display: flex; gap: 0.5rem;">
            <a href="export.php" class="btn btn-sm" style="background: #27ae60; color: white;">📥 Download Excel</a>
            <a href="add.php" class="btn btn-add btn-sm">+ Add New</a>
        </div>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Certificate</th>
                <th>Name</th>
                <th>Issuer</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="6" style="text-align: center;">No certifications found</td></tr>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <?php $isPdf = !empty($item['image_certificate']) && strtolower(pathinfo($item['image_certificate'], PATHINFO_EXTENSION)) === 'pdf'; ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td>
                            <?php if (!empty($item['image_certificate'])): ?>
                                <?php if ($isPdf): ?>
                                    <span class="pdf-badge">📄 PDF</span>
                                <?php else: ?>
                                    <img src="/portofolio/assets/images/<?php echo htmlspecialchars($item['image_certificate']); ?>" class="cert-thumbnail" alt="Cert">
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color: #999;">No File</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($item['name_certificate'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($item['issuer'] ?? '-'); ?></td>
                        <td><?php echo !empty($item['issue_date']) ? date('M Y', strtotime($item['issue_date'])) : '-'; ?></td>
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
