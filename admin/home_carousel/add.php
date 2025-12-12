<?php
/**
 * Admin - Add Home Carousel
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Add Carousel Item';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $image_path = '';
    
    if (!empty($_FILES['image']['name'])) {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
        $filename = time() . '_carousel_' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $filename)) {
            $image_path = $filename;
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO home_carousel (image_path, title, subtitle) VALUES (?, ?, ?)");
    $stmt->execute([$image_path, $title, $subtitle]);
    header('Location: index.php?added=1');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

<div class="form-card">
    <h3 style="margin-bottom: 1.5rem;">Add Carousel Item</h3>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="image">Carousel Image</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*" required>
        </div>
        
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="subtitle">Subtitle</label>
            <input type="text" id="subtitle" name="subtitle" class="form-control">
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Add Item</button>
            <a href="index.php" class="btn btn-outline" style="color: var(--primary); border-color: var(--primary);">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
