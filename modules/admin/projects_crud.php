<?php
// Helper function for multiple file upload
function uploadMultipleImages($files, $target_dir = "assets/images/uploads/") {
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
if (isset($_POST['save_project'])) {
    $description = $_POST['description'];
    $project_link_github = $_POST['project_link_github'];
    $id = $_POST['id'];

    // Handle Project Images (Multi)
    $existing_images = isset($_POST['existing_project_images']) ? json_decode($_POST['existing_project_images'], true) : [];
    if (!is_array($existing_images)) $existing_images = []; // Safety check
    
    $new_images = uploadMultipleImages($_FILES['project_image_files']);
    $final_project_images = array_merge($existing_images, $new_images);
    $project_image_json = json_encode($final_project_images);

    // Handle Tool Logo (Single - but using same logic for consistency or keep simple?)
    // Tool logo is usually single. Let's keep it simple or allow multi? Schema says TEXT now.
    // Let's assume tool logo is still single for now, or maybe multi if they used many tools.
    // Let's make it multi too for flexibility.
    $existing_tools = isset($_POST['existing_tool_logos']) ? json_decode($_POST['existing_tool_logos'], true) : [];
    if (!is_array($existing_tools)) $existing_tools = [];
    
    $new_tools = uploadMultipleImages($_FILES['tool_logo_files']);
    $final_tool_logos = array_merge($existing_tools, $new_tools);
    $tool_logo_json = json_encode($final_tool_logos);

    if ($id) {
        $stmt = $pdo->prepare("UPDATE project_page SET project_image=?, tool_logo=?, description=?, project_link_github=? WHERE id=?");
        $stmt->execute([$project_image_json, $tool_logo_json, $description, $project_link_github, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO project_page (project_image, tool_logo, description, project_link_github) VALUES (?, ?, ?, ?)");
        $stmt->execute([$project_image_json, $tool_logo_json, $description, $project_link_github]);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM project_page WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: ?page=admin&tab=projects");
    exit;
}

// Handle Image Removal (AJAX-like or simple GET for now)
if (isset($_GET['remove_image']) && isset($_GET['id']) && isset($_GET['type'])) {
    $id = $_GET['id'];
    $image_to_remove = $_GET['remove_image'];
    $type = $_GET['type']; // 'project' or 'tool'
    
    $stmt = $pdo->prepare("SELECT * FROM project_page WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($item) {
        $column = ($type == 'project') ? 'project_image' : 'tool_logo';
        $images = json_decode($item[$column], true);
        if (is_array($images)) {
            $key = array_search($image_to_remove, $images);
            if ($key !== false) {
                unset($images[$key]);
                $new_json = json_encode(array_values($images));
                $update = $pdo->prepare("UPDATE project_page SET $column = ? WHERE id = ?");
                $update->execute([$new_json, $id]);
            }
        }
    }
    header("Location: ?page=admin&tab=projects&edit=$id");
    exit;
}

// Fetch Data
$stmt = $pdo->query("SELECT * FROM project_page");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$edit_item = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM project_page WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_item = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Manage Projects</h2>
<p>Add or edit your portfolio projects.</p>

<div class="glass-card" style="background: rgba(255,255,255,0.05); padding: 20px; margin-top: 20px;">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $edit_item['id'] ?? '' ?>">
        
        <!-- Project Images -->
        <div class="form-group">
            <label>Project Images (Select Multiple)</label>
            <div class="file-upload-wrapper">
                <input type="file" name="project_image_files[]" multiple accept="image/*" onchange="previewFiles(this, 'project-preview')">
                <div class="file-upload-content">
                    <div class="file-upload-icon">&#128247;</div>
                    <div class="file-upload-text">Drag & Drop or Click to Upload</div>
                </div>
            </div>
            <div id="project-preview" class="preview-container"></div>
            
            <!-- Existing Images -->
            <?php 
            $existing_imgs = isset($edit_item['project_image']) ? json_decode($edit_item['project_image'], true) : [];
            if (!is_array($existing_imgs) && !empty($edit_item['project_image'])) $existing_imgs = [$edit_item['project_image']]; // Backwards compatibility
            ?>
            <input type="hidden" name="existing_project_images" value='<?= json_encode($existing_imgs) ?>'>
            <?php if (!empty($existing_imgs)): ?>
                <div class="preview-container">
                    <?php foreach ($existing_imgs as $img): ?>
                        <div style="position: relative;">
                            <img src="<?= htmlspecialchars($img) ?>" class="preview-image">
                            <a href="?page=admin&tab=projects&remove_image=<?= urlencode($img) ?>&id=<?= $edit_item['id'] ?>&type=project" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 20px; text-decoration: none; font-size: 12px;">&times;</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tool Logos -->
        <div class="form-group">
            <label>Tool Logos (Select Multiple)</label>
            <div class="file-upload-wrapper">
                <input type="file" name="tool_logo_files[]" multiple accept="image/*" onchange="previewFiles(this, 'tool-preview')">
                <div class="file-upload-content">
                    <div class="file-upload-icon">&#128736;</div>
                    <div class="file-upload-text">Drag & Drop or Click to Upload</div>
                </div>
            </div>
            <div id="tool-preview" class="preview-container"></div>
            
            <?php 
            $existing_tools = isset($edit_item['tool_logo']) ? json_decode($edit_item['tool_logo'], true) : [];
            if (!is_array($existing_tools) && !empty($edit_item['tool_logo'])) $existing_tools = [$edit_item['tool_logo']];
            ?>
            <input type="hidden" name="existing_tool_logos" value='<?= json_encode($existing_tools) ?>'>
            <?php if (!empty($existing_tools)): ?>
                <div class="preview-container">
                    <?php foreach ($existing_tools as $img): ?>
                        <div style="position: relative;">
                            <img src="<?= htmlspecialchars($img) ?>" class="preview-image">
                            <a href="?page=admin&tab=projects&remove_image=<?= urlencode($img) ?>&id=<?= $edit_item['id'] ?>&type=tool" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 20px; text-decoration: none; font-size: 12px;">&times;</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" required><?= $edit_item['description'] ?? '' ?></textarea>
        </div>
        <div class="form-group">
            <label>GitHub Link</label>
            <input type="text" name="project_link_github" class="form-control" value="<?= $edit_item['project_link_github'] ?? '' ?>" required>
        </div>
        <button type="submit" name="save_project" class="btn-primary"><?= $edit_item ? 'Update' : 'Add New' ?></button>
        <?php if ($edit_item): ?>
            <a href="?page=admin&tab=projects" class="btn-sm" style="background: #ccc; color: #000;">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Images</th>
                <th>Tools</th>
                <th>Description</th>
                <th>Link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): 
                $p_imgs = json_decode($item['project_image'], true);
                if (!is_array($p_imgs)) $p_imgs = [$item['project_image']];
                
                $t_imgs = json_decode($item['tool_logo'], true);
                if (!is_array($t_imgs)) $t_imgs = [$item['tool_logo']];
            ?>
            <tr>
                <td>
                    <?php if (!empty($p_imgs[0])): ?>
                        <img src="<?= htmlspecialchars($p_imgs[0]) ?>" style="width: 50px; height: 30px; object-fit: cover;">
                        <?php if(count($p_imgs) > 1) echo '<small>+'.(count($p_imgs)-1).'</small>'; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($t_imgs[0])): ?>
                        <img src="<?= htmlspecialchars($t_imgs[0]) ?>" style="width: 20px; height: 20px;">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars(substr($item['description'], 0, 50)) ?>...</td>
                <td><a href="<?= htmlspecialchars($item['project_link_github']) ?>" target="_blank" style="color: var(--accent-color);">Link</a></td>
                <td>
                    <a href="?page=admin&tab=projects&edit=<?= $item['id'] ?>" class="btn-sm btn-edit">Edit</a>
                    <a href="?page=admin&tab=projects&delete=<?= $item['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function previewFiles(input, previewId) {
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
