<?php
/**
 * Admin - Add Profile/Dashboard
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Add Profile Item';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carrousel_teks = $_POST['carrousel_teks'] ?? '';
    $carrousel_image = '';
    $photo_profil = '';
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    if (!empty($_FILES['carrousel_image']['name'])) {
        $filename = time() . '_carousel_' . basename($_FILES['carrousel_image']['name']);
        if (move_uploaded_file($_FILES['carrousel_image']['tmp_name'], $target_dir . $filename)) {
            $carrousel_image = $filename;
        }
    }
    
    if (!empty($_FILES['photo_profil']['name'])) {
        $filename = time() . '_profile_' . basename($_FILES['photo_profil']['name']);
        if (move_uploaded_file($_FILES['photo_profil']['tmp_name'], $target_dir . $filename)) {
            $photo_profil = $filename;
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO dashboard (carrousel_image, carrousel_teks, photo_profil) VALUES (?, ?, ?)");
    $stmt->execute([$carrousel_image, $carrousel_teks, $photo_profil]);
    header('Location: index.php?added=1');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

<div class="form-card">
    <h3 style="margin-bottom: 1.5rem;">Add Profile/Dashboard Item</h3>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="photo_profil">Profile Photo</label>
            <input type="file" id="photo_profil" name="photo_profil" class="form-control" accept="image/*">
        </div>
        
        <div class="form-group">
            <label for="carrousel_image">Carousel Image</label>
            <input type="file" id="carrousel_image" name="carrousel_image" class="form-control" accept="image/*">
        </div>
        
        <div class="form-group">
            <label for="carrousel_teks">Text/Description</label>
            <textarea id="carrousel_teks" name="carrousel_teks" class="form-control" rows="4"></textarea>
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Add</button>
            <a href="index.php" class="btn btn-outline" style="color: var(--primary); border-color: var(--primary);">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
