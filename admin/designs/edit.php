<?php
/**
 * Admin - Edit Design
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Edit Design';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM design_page WHERE id = ?");
$stmt->execute([$id]);
$design = $stmt->fetch();

if (!$design) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $design_link = $_POST['design_link'] ?? '';
    $design_image = $design['design_image'];
    
    if (!empty($_FILES['design_image']['name'])) {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
        $filename = time() . '_' . basename($_FILES['design_image']['name']);
        if (move_uploaded_file($_FILES['design_image']['tmp_name'], $target_dir . $filename)) {
            $design_image = $filename;
        }
    }
    
    $stmt = $pdo->prepare("UPDATE design_page SET design_image = ?, design_link = ? WHERE id = ?");
    $stmt->execute([$design_image, $design_link, $id]);
    header('Location: index.php?updated=1');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

<div class="form-card">
    <h3 style="margin-bottom: 1.5rem;">Edit Design</h3>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="design_image">Design Image</label>
            <?php if ($design['design_image']): ?>
                <div style="margin-bottom: 0.5rem;">
                    <img src="/portofolio/assets/images/<?php echo htmlspecialchars($design['design_image']); ?>" style="max-width: 150px; border-radius: 8px;">
                </div>
            <?php endif; ?>
            <input type="file" id="design_image" name="design_image" class="form-control" accept="image/*">
        </div>
        
        <div class="form-group">
            <label for="design_link">Design Link</label>
            <input type="url" id="design_link" name="design_link" class="form-control" value="<?php echo htmlspecialchars($design['design_link'] ?? ''); ?>">
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Update Design</button>
            <a href="index.php" class="btn btn-outline" style="color: var(--primary); border-color: var(--primary);">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
