<?php
function uploadMultipleImagesEdu($files, $target_dir = "assets/images/uploads/") {
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
if (isset($_POST['save_education'])) {
    $category = $_POST['category'];
    $name_education_history = $_POST['name_education_history'];
    $name_certificate = $_POST['name_certificate'];
    $link_certificate = $_POST['link_certificate'];
    $id = $_POST['id'];

    // Handle Activity Images
    $existing_activity = isset($_POST['existing_image_activity']) ? json_decode($_POST['existing_image_activity'], true) : [];
    if (!is_array($existing_activity)) $existing_activity = [];
    $new_activity = uploadMultipleImagesEdu($_FILES['image_activity_files']);
    $final_activity = array_merge($existing_activity, $new_activity);
    $image_activity_json = json_encode($final_activity);

    // Handle Certificate Images
    $existing_cert = isset($_POST['existing_image_certificate']) ? json_decode($_POST['existing_image_certificate'], true) : [];
    if (!is_array($existing_cert)) $existing_cert = [];
    $new_cert = uploadMultipleImagesEdu($_FILES['image_certificate_files']);
    $final_cert = array_merge($existing_cert, $new_cert);
    $image_certificate_json = json_encode($final_cert);

    if ($id) {
        $stmt = $pdo->prepare("UPDATE education_and_certification_page SET category=?, name_education_history=?, image_activity=?, name_certificate=?, image_certificate=?, link_certificate=? WHERE id=?");
        $stmt->execute([$category, $name_education_history, $image_activity_json, $name_certificate, $image_certificate_json, $link_certificate, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO education_and_certification_page (category, name_education_history, image_activity, name_certificate, image_certificate, link_certificate) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$category, $name_education_history, $image_activity_json, $name_certificate, $image_certificate_json, $link_certificate]);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM education_and_certification_page WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: ?page=admin&tab=education");
    exit;
}

// Handle Image Removal
if (isset($_GET['remove_image']) && isset($_GET['id']) && isset($_GET['type'])) {
    $id = $_GET['id'];
    $image_to_remove = $_GET['remove_image'];
    $type = $_GET['type']; // 'activity' or 'certificate'
    
    $stmt = $pdo->prepare("SELECT * FROM education_and_certification_page WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($item) {
        $column = ($type == 'activity') ? 'image_activity' : 'image_certificate';
        $images = json_decode($item[$column], true);
        if (is_array($images)) {
            $key = array_search($image_to_remove, $images);
            if ($key !== false) {
                unset($images[$key]);
                $new_json = json_encode(array_values($images));
                $update = $pdo->prepare("UPDATE education_and_certification_page SET $column = ? WHERE id = ?");
                $update->execute([$new_json, $id]);
            }
        }
    }
    header("Location: ?page=admin&tab=education&edit=$id");
    exit;
}

// Fetch Data
$stmt = $pdo->query("SELECT * FROM education_and_certification_page");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$edit_item = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM education_and_certification_page WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_item = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Manage Education & Certifications</h2>
<p>Add your academic history or professional certificates.</p>

<div class="glass-card" style="background: rgba(255,255,255,0.05); padding: 20px; margin-top: 20px;">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $edit_item['id'] ?? '' ?>">
        <div class="form-group">
            <label>Category</label>
            <select name="category" class="form-control" required>
                <option value="Education" <?= ($edit_item['category'] ?? '') == 'Education' ? 'selected' : '' ?>>Education</option>
                <option value="Certification" <?= ($edit_item['category'] ?? '') == 'Certification' ? 'selected' : '' ?>>Certification</option>
            </select>
        </div>
        <div class="form-group">
            <label>Name (Education/Certificate)</label>
            <input type="text" name="name_education_history" class="form-control" value="<?= $edit_item['name_education_history'] ?? '' ?>">
        </div>
        
        <div class="form-group">
            <label>Activity Images</label>
            <div class="file-upload-wrapper">
                <input type="file" name="image_activity_files[]" multiple accept="image/*" onchange="previewFilesEdu(this, 'activity-preview')">
                <div class="file-upload-content">
                    <div class="file-upload-icon">&#127891;</div>
                    <div class="file-upload-text">Drag & Drop or Click to Upload</div>
                </div>
            </div>
            <div id="activity-preview" class="preview-container"></div>
            
            <?php 
            $existing_act = isset($edit_item['image_activity']) ? json_decode($edit_item['image_activity'], true) : [];
            if (!is_array($existing_act) && !empty($edit_item['image_activity'])) $existing_act = [$edit_item['image_activity']];
            ?>
            <input type="hidden" name="existing_image_activity" value='<?= json_encode($existing_act) ?>'>
            <?php if (!empty($existing_act)): ?>
                <div class="preview-container">
                    <?php foreach ($existing_act as $img): ?>
                        <div style="position: relative;">
                            <img src="<?= htmlspecialchars($img) ?>" class="preview-image">
                            <a href="?page=admin&tab=education&remove_image=<?= urlencode($img) ?>&id=<?= $edit_item['id'] ?>&type=activity" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 20px; text-decoration: none; font-size: 12px;">&times;</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label>Certificate Name (Alternate)</label>
            <input type="text" name="name_certificate" class="form-control" value="<?= $edit_item['name_certificate'] ?? '' ?>">
        </div>

        <div class="form-group">
            <label>Certificate Images</label>
            <div class="file-upload-wrapper">
                <input type="file" name="image_certificate_files[]" multiple accept="image/*" onchange="previewFilesEdu(this, 'cert-preview')">
                <div class="file-upload-content">
                    <div class="file-upload-icon">&#128196;</div>
                    <div class="file-upload-text">Drag & Drop or Click to Upload</div>
                </div>
            </div>
            <div id="cert-preview" class="preview-container"></div>
            
            <?php 
            $existing_cert = isset($edit_item['image_certificate']) ? json_decode($edit_item['image_certificate'], true) : [];
            if (!is_array($existing_cert) && !empty($edit_item['image_certificate'])) $existing_cert = [$edit_item['image_certificate']];
            ?>
            <input type="hidden" name="existing_image_certificate" value='<?= json_encode($existing_cert) ?>'>
            <?php if (!empty($existing_cert)): ?>
                <div class="preview-container">
                    <?php foreach ($existing_cert as $img): ?>
                        <div style="position: relative;">
                            <img src="<?= htmlspecialchars($img) ?>" class="preview-image">
                            <a href="?page=admin&tab=education&remove_image=<?= urlencode($img) ?>&id=<?= $edit_item['id'] ?>&type=certificate" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 20px; text-decoration: none; font-size: 12px;">&times;</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Certificate Link</label>
            <input type="text" name="link_certificate" class="form-control" value="<?= $edit_item['link_certificate'] ?? '' ?>">
        </div>

        <button type="submit" name="save_education" class="btn-primary"><?= $edit_item ? 'Update' : 'Add New' ?></button>
        <?php if ($edit_item): ?>
            <a href="?page=admin&tab=education" class="btn-sm" style="background: #ccc; color: #000;">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Name</th>
                <th>Images</th>
                <th>Link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): 
                $act_imgs = json_decode($item['image_activity'], true);
                if (!is_array($act_imgs)) $act_imgs = [$item['image_activity']];
                
                $cert_imgs = json_decode($item['image_certificate'], true);
                if (!is_array($cert_imgs)) $cert_imgs = [$item['image_certificate']];
                
                $display_img = !empty($act_imgs[0]) ? $act_imgs[0] : $cert_imgs[0];
            ?>
            <tr>
                <td><?= htmlspecialchars($item['category']) ?></td>
                <td><?= htmlspecialchars($item['name_education_history'] ?: $item['name_certificate']) ?></td>
                <td>
                    <?php if ($display_img): ?>
                        <img src="<?= htmlspecialchars($display_img) ?>" style="width: 30px; height: 30px; object-fit: cover;">
                    <?php endif; ?>
                </td>
                <td><a href="<?= htmlspecialchars($item['link_certificate']) ?>" target="_blank" style="color: var(--accent-color);">Link</a></td>
                <td>
                    <a href="?page=admin&tab=education&edit=<?= $item['id'] ?>" class="btn-sm btn-edit">Edit</a>
                    <a href="?page=admin&tab=education&delete=<?= $item['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function previewFilesEdu(input, previewId) {
    const preview = document.getElementById(previewId);
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
