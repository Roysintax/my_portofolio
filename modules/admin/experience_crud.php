<?php
function uploadMultipleImagesExp($files, $target_dir = "assets/images/uploads/") {
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
if (isset($_POST['save_experience'])) {
    $name_company = $_POST['name_company'];
    $date_work = $_POST['date_work'];
    $activity_description = $_POST['activity_description'];
    $id = $_POST['id'];

    // Handle Images
    $existing_images = isset($_POST['existing_images']) ? json_decode($_POST['existing_images'], true) : [];
    if (!is_array($existing_images)) $existing_images = [];
    $new_images = uploadMultipleImagesExp($_FILES['image_files']);
    $final_images = array_merge($existing_images, $new_images);
    $image_json = json_encode($final_images);

    if ($id) {
        $stmt = $pdo->prepare("UPDATE work_experience_page SET name_company=?, date_work=?, activity_description=?, image_activity_work_carrousel=? WHERE id=?");
        $stmt->execute([$name_company, $date_work, $activity_description, $image_json, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO work_experience_page (name_company, date_work, activity_description, image_activity_work_carrousel) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name_company, $date_work, $activity_description, $image_json]);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM work_experience_page WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: ?page=admin&tab=experience");
    exit;
}

// Handle Image Removal
if (isset($_GET['remove_image']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $image_to_remove = $_GET['remove_image'];
    
    $stmt = $pdo->prepare("SELECT * FROM work_experience_page WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($item) {
        $images = json_decode($item['image_activity_work_carrousel'], true);
        if (is_array($images)) {
            $key = array_search($image_to_remove, $images);
            if ($key !== false) {
                unset($images[$key]);
                $new_json = json_encode(array_values($images));
                $update = $pdo->prepare("UPDATE work_experience_page SET image_activity_work_carrousel = ? WHERE id = ?");
                $update->execute([$new_json, $id]);
            }
        }
    }
    header("Location: ?page=admin&tab=experience&edit=$id");
    exit;
}

// Fetch Data
$stmt = $pdo->query("SELECT * FROM work_experience_page ORDER BY date_work DESC");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$edit_item = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM work_experience_page WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_item = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Manage Work Experience</h2>
<p>Track your professional journey.</p>

<div class="glass-card" style="background: rgba(255,255,255,0.05); padding: 20px; margin-top: 20px;">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $edit_item['id'] ?? '' ?>">
        <div class="form-group">
            <label>Company Name</label>
            <input type="text" name="name_company" class="form-control" value="<?= $edit_item['name_company'] ?? '' ?>" required>
        </div>
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date_work" class="form-control" value="<?= $edit_item['date_work'] ?? '' ?>" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="activity_description" class="form-control" rows="3" required><?= $edit_item['activity_description'] ?? '' ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Activity Images</label>
            <div class="file-upload-wrapper">
                <input type="file" name="image_files[]" multiple accept="image/*" onchange="previewFilesExp(this)">
                <div class="file-upload-content">
                    <div class="file-upload-icon">&#128188;</div>
                    <div class="file-upload-text">Drag & Drop or Click to Upload</div>
                </div>
            </div>
            <div id="exp-preview" class="preview-container"></div>
            
            <?php 
            $existing_imgs = isset($edit_item['image_activity_work_carrousel']) ? json_decode($edit_item['image_activity_work_carrousel'], true) : [];
            if (!is_array($existing_imgs) && !empty($edit_item['image_activity_work_carrousel'])) $existing_imgs = [$edit_item['image_activity_work_carrousel']];
            ?>
            <input type="hidden" name="existing_images" value='<?= json_encode($existing_imgs) ?>'>
            <?php if (!empty($existing_imgs)): ?>
                <div class="preview-container">
                    <?php foreach ($existing_imgs as $img): ?>
                        <div style="position: relative;">
                            <img src="<?= htmlspecialchars($img) ?>" class="preview-image">
                            <a href="?page=admin&tab=experience&remove_image=<?= urlencode($img) ?>&id=<?= $edit_item['id'] ?>" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 20px; text-decoration: none; font-size: 12px;">&times;</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" name="save_experience" class="btn-primary"><?= $edit_item ? 'Update' : 'Add New' ?></button>
        <?php if ($edit_item): ?>
            <a href="?page=admin&tab=experience" class="btn-sm" style="background: #ccc; color: #000;">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Company</th>
                <th>Date</th>
                <th>Images</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): 
                $imgs = json_decode($item['image_activity_work_carrousel'], true);
                if (!is_array($imgs)) $imgs = [$item['image_activity_work_carrousel']];
            ?>
            <tr>
                <td><?= htmlspecialchars($item['name_company']) ?></td>
                <td><?= htmlspecialchars($item['date_work']) ?></td>
                <td>
                    <?php if (!empty($imgs[0])): ?>
                        <img src="<?= htmlspecialchars($imgs[0]) ?>" style="width: 30px; height: 30px; object-fit: cover;">
                        <?php if(count($imgs) > 1) echo '<small>+'.(count($imgs)-1).'</small>'; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="?page=admin&tab=experience&edit=<?= $item['id'] ?>" class="btn-sm btn-edit">Edit</a>
                    <a href="?page=admin&tab=experience&delete=<?= $item['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function previewFilesExp(input) {
    const preview = document.getElementById('exp-preview');
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
