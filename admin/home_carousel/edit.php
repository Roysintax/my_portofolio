<?php
/**
 * Admin - Edit Home Carousel
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Edit Carousel Item';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM home_carousel WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $image_path = $item['image_path'];
    
    if (!empty($_FILES['image']['name'])) {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
        $filename = time() . '_carousel_' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $filename)) {
            $image_path = $filename;
        }
    }
    
    $stmt = $pdo->prepare("UPDATE home_carousel SET image_path = ?, title = ?, subtitle = ? WHERE id = ?");
    $stmt->execute([$image_path, $title, $subtitle, $id]);
    header('Location: index.php?updated=1');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

<div class="form-card">
    <h3 style="margin-bottom: 1.5rem;">Edit Carousel Item</h3>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="image">Carousel Image</label>
            <?php if ($item['image_path']): ?>
                <div style="margin-bottom: 0.5rem;">
                    <img src="/portofolio/assets/images/<?php echo htmlspecialchars($item['image_path']); ?>" style="max-width: 200px; border-radius: 8px;">
                </div>
            <?php endif; ?>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
        </div>
        
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="subtitle">Subtitle</label>
            <input type="text" id="subtitle" name="subtitle" class="form-control" value="<?php echo htmlspecialchars($item['subtitle'] ?? ''); ?>">
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="index.php" class="btn btn-outline" style="color: var(--primary); border-color: var(--primary);">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
