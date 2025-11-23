<?php
// Helper function (Duplicated for modularity)
function uploadMultipleImagesDesign($files, $target_dir = "assets/images/uploads/") {
    $uploaded_paths = [];
    if (isset($files['name']) && is_array($files['name'])) {
        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] == 0) {
                $target_file = $target_dir . basename($files["name"][$i]);
                $check = getimagesize($files["tmp_name"][$i]);
                if($check !== false) {
                    if (move_uploaded_file($files["tmp_name"][$i], $target_file)) {
                        $uploaded_paths[] = $target_file;
                    }
                }
            }
        }
    }
    return $uploaded_paths;
}

// Handle Create/Update
if (isset($_POST['save_design'])) {
    $design_link = $_POST['design_link'];
    $id = $_POST['id'];

    // Handle Images
    $existing_images = isset($_POST['existing_design_images']) ? json_decode($_POST['existing_design_images'], true) : [];
    if (!is_array($existing_images)) $existing_images = [];
    
    $new_images = uploadMultipleImagesDesign($_FILES['design_image_files']);
    $final_images = array_merge($existing_images, $new_images);
    $design_image_json = json_encode($final_images);

    if ($id) {
        $stmt = $pdo->prepare("UPDATE design_page SET design_image=?, design_link=? WHERE id=?");
        $stmt->execute([$design_image_json, $design_link, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO design_page (design_image, design_link) VALUES (?, ?)");
        $stmt->execute([$design_image_json, $design_link]);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM design_page WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: ?page=admin&tab=designs");
    exit;
}

// Handle Image Removal
if (isset($_GET['remove_image']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $image_to_remove = $_GET['remove_image'];
    
    $stmt = $pdo->prepare("SELECT * FROM design_page WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($item) {
        $images = json_decode($item['design_image'], true);
        if (is_array($images)) {
            $key = array_search($image_to_remove, $images);
            if ($key !== false) {
                unset($images[$key]);
                $new_json = json_encode(array_values($images));
                $update = $pdo->prepare("UPDATE design_page SET design_image = ? WHERE id = ?");
                $update->execute([$new_json, $id]);
            }
        }
    }
    header("Location: ?page=admin&tab=designs&edit=$id");
    exit;
}

// Fetch Data
$stmt = $pdo->query("SELECT * FROM design_page");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$edit_item = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM design_page WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_item = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Manage Designs</h2>
<p>Showcase your design work.</p>

<div class="glass-card" style="background: rgba(255,255,255,0.05); padding: 20px; margin-top: 20px;">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $edit_item['id'] ?? '' ?>">
        
        <div class="form-group">
            <label>Design Images (Select Multiple)</label>
            <div class="file-upload-wrapper">
                <input type="file" name="design_image_files[]" multiple accept="image/*" onchange="previewFilesDesign(this)">
                <div class="file-upload-content">
                    <div class="file-upload-icon">&#127912;</div>
                    <div class="file-upload-text">Drag & Drop or Click to Upload</div>
                </div>
            </div>
            <div id="design-preview" class="preview-container"></div>
            
            <?php 
            $existing_imgs = isset($edit_item['design_image']) ? json_decode($edit_item['design_image'], true) : [];
            if (!is_array($existing_imgs) && !empty($edit_item['design_image'])) $existing_imgs = [$edit_item['design_image']];
            ?>
            <input type="hidden" name="existing_design_images" value='<?= json_encode($existing_imgs) ?>'>
            <?php if (!empty($existing_imgs)): ?>
                <div class="preview-container">
                    <?php foreach ($existing_imgs as $img): ?>
                        <div style="position: relative;">
                            <img src="<?= htmlspecialchars($img) ?>" class="preview-image">
                            <a href="?page=admin&tab=designs&remove_image=<?= urlencode($img) ?>&id=<?= $edit_item['id'] ?>" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 20px; text-decoration: none; font-size: 12px;">&times;</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Design Link (Optional)</label>
            <input type="text" name="design_link" class="form-control" value="<?= $edit_item['design_link'] ?? '' ?>">
        </div>
        <button type="submit" name="save_design" class="btn-primary"><?= $edit_item ? 'Update' : 'Add New' ?></button>
        <?php if ($edit_item): ?>
            <a href="?page=admin&tab=designs" class="btn-sm" style="background: #ccc; color: #000;">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Images</th>
                <th>Link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): 
                $imgs = json_decode($item['design_image'], true);
                if (!is_array($imgs)) $imgs = [$item['design_image']];
            ?>
            <tr>
                <td>
                    <?php if (!empty($imgs[0])): ?>
                        <img src="<?= htmlspecialchars($imgs[0]) ?>" style="width: 50px; height: 30px; object-fit: cover;">
                        <?php if(count($imgs) > 1) echo '<small>+'.(count($imgs)-1).'</small>'; ?>
                    <?php endif; ?>
                </td>
                <td><a href="<?= htmlspecialchars($item['design_link']) ?>" target="_blank" style="color: var(--accent-color);">Link</a></td>
                <td>
                    <a href="?page=admin&tab=designs&edit=<?= $item['id'] ?>" class="btn-sm btn-edit">Edit</a>
                    <a href="?page=admin&tab=designs&delete=<?= $item['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function previewFilesDesign(input) {
    const preview = document.getElementById('design-preview');
    preview.innerHTML = '';
    if (input.files) {
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-image';
                preview.appendChild(img);
            }
            reader.readAsDataURL(file);
        });
    }
}
</script>
