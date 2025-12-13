<?php
/**
 * Admin - Education History Management
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Education History';

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM education_history WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: index.php?deleted=1');
    exit;
}

$items = $pdo->query("SELECT * FROM education_history ORDER BY id DESC")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="data-card">
    <div class="data-card-header">
        <h3>📚 Education History</h3>
        <div style="display: flex; gap: 0.5rem;">
            <a href="export.php" class="btn btn-sm" style="background: #27ae60; color: white;">📥 Download Excel</a>
            <a href="add.php" class="btn btn-add btn-sm">+ Add New</a>
        </div>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Education Name</th>
                <th>Period</th>
                <th>Description</th>
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
                        <td><?php echo htmlspecialchars($item['name_education'] ?? '-'); ?></td>
                        <td>
                            <?php
                            $start = !empty($item['start_date']) ? date('M Y', strtotime($item['start_date'])) : '-';
                            if (!empty($item['still_studying']) && $item['still_studying'] == 1) {
                                $end = '<span style="color: #4caf50; font-weight: 600;">Present</span>';
                            } elseif (!empty($item['end_date'])) {
                                $end = date('M Y', strtotime($item['end_date']));
                            } else {
                                $end = '-';
                            }
                            echo $start . ' - ' . $end;
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars(substr($item['description'] ?? '', 0, 50)) . '...'; ?></td>
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
