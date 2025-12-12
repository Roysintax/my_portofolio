<?php
/**
 * Admin - Work Experience Management
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Manage Work Experience';

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM work_experience_page WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: index.php?deleted=1');
    exit;
}

$items = $pdo->query("SELECT * FROM work_experience_page ORDER BY date_work_start DESC")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="data-card">
    <div class="data-card-header">
        <h3>All Work Experience</h3>
        <div style="display: flex; gap: 0.5rem;">
            <a href="export.php" class="btn btn-sm" style="background: #27ae60; color: white;">📥 Download Excel</a>
            <a href="add.php" class="btn btn-add btn-sm">+ Add New</a>
        </div>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Company</th>
                <th>Status</th>
                <th>Period</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="6" style="text-align: center;">No items found</td></tr>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo htmlspecialchars($item['name_company'] ?? '-'); ?></td>
                        <td>
                            <?php if (!empty($item['work_status'])): ?>
                                <span style="padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; 
                                    background: <?php echo $item['work_status'] === 'magang' ? '#f093fb' : '#667eea'; ?>; color: white;">
                                    <?php echo $item['work_status'] === 'magang' ? '📚 Magang' : '💼 Kerja'; ?>
                                </span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $start = !empty($item['date_work_start']) ? date('M Y', strtotime($item['date_work_start'])) : '-';
                            if (!empty($item['still_working']) && $item['still_working'] == 1) {
                                if ($item['work_status'] === 'magang') {
                                    $end = '<span style="color: #ff9800; font-weight: 600;">🟢 Masih Magang</span>';
                                } else {
                                    $end = '<span style="color: #4caf50; font-weight: 600;">🟢 Masih Bekerja</span>';
                                }
                            } elseif (!empty($item['date_work_end'])) {
                                $end = date('M Y', strtotime($item['date_work_end']));
                            } else {
                                $end = '-';
                            }
                            echo $start . ' - ' . $end;
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars(substr($item['activity_description'] ?? '', 0, 50)) . '...'; ?></td>
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
