<?php
/**
 * Admin - Edit Project (with Multiple Tool Logos)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Edit Project';
$error = '';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM project_page WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    header('Location: index.php');
    exit;
}

// Parse existing logos
$existing_logos = [];
if (!empty($project['tool_logo'])) {
    $existing_logos = array_filter(array_map('trim', explode(',', $project['tool_logo'])));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'] ?? '';
    $github_link = $_POST['github_link'] ?? '';
    $project_image = $project['project_image'];
    
    // Keep existing logos that weren't removed
    $kept_logos = $_POST['kept_logos'] ?? [];
    $tool_logos = $kept_logos;
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    // Handle project image upload
    if (!empty($_FILES['project_image']['name'])) {
        $filename = time() . '_' . basename($_FILES['project_image']['name']);
        if (move_uploaded_file($_FILES['project_image']['tmp_name'], $target_dir . $filename)) {
            $project_image = $filename;
        }
    }
    
    // Handle new tool logo uploads
    if (!empty($_FILES['tool_logos']['name'][0])) {
        $file_count = count($_FILES['tool_logos']['name']);
        
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['tool_logos']['error'][$i] === UPLOAD_ERR_OK) {
                $filename = time() . '_tool_' . $i . '_' . basename($_FILES['tool_logos']['name'][$i]);
                if (move_uploaded_file($_FILES['tool_logos']['tmp_name'][$i], $target_dir . $filename)) {
                    $tool_logos[] = $filename;
                }
            }
        }
    }
    
    $tool_logo_string = implode(',', $tool_logos);
    
    $stmt = $pdo->prepare("UPDATE project_page SET project_image = ?, tool_logo = ?, description = ?, project_link_github = ? WHERE id = ?");
    if ($stmt->execute([$project_image, $tool_logo_string, $description, $github_link, $id])) {
        header('Location: index.php?updated=1');
        exit;
    } else {
        $error = 'Failed to update project';
    }
}

include __DIR__ . '/../includes/header.php';
?>

<style>
.logo-input-group {
    background: #f8f9fa;
    border: 2px dashed #ddd;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
}

.logo-input-group:hover {
    border-color: var(--accent);
    background: #fff5f6;
}

.logo-input-group .logo-number {
    background: var(--gradient-accent);
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.btn-remove-logo {
    background: #e74c3c;
    color: white;
    border: none;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.btn-add-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px dashed var(--accent);
    border-radius: 10px;
    cursor: pointer;
    color: var(--accent);
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    width: fit-content;
}

.btn-add-logo:hover {
    background: linear-gradient(135deg, #fff5f6 0%, #ffe8eb 100%);
}

.btn-add-logo .plus-icon {
    width: 24px;
    height: 24px;
    background: var(--gradient-accent);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.logo-preview {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    object-fit: cover;
    flex-shrink: 0;
}

.existing-logo {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #e8f4e8;
    border: 2px solid #27ae60;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    margin-bottom: 0.75rem;
}

.existing-logo img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 6px;
}

.existing-logo .info {
    flex: 1;
    font-size: 0.85rem;
    color: #27ae60;
}

.tools-section {
    background: #f0f4f8;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}

.tools-section h5 {
    margin: 0 0 1rem 0;
    color: var(--primary);
    font-size: 0.95rem;
}
</style>

<div class="form-card">
    <div class="form-card-header">
        <h3>✏️ Edit Project #<?php echo $project['id']; ?></h3>
        <p>Update your project information</p>
    </div>
    
    <div class="form-card-body">
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <span>⚠️</span>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="project_image">Project Image</label>
                <?php if ($project['project_image']): ?>
                    <div class="file-preview">
                        <img src="/portofolio/assets/images/<?php echo htmlspecialchars($project['project_image']); ?>" style="max-width: 200px; max-height: 150px;">
                    </div>
                <?php endif; ?>
                <div class="file-upload-wrapper">
                    <input type="file" id="project_image" name="project_image" class="form-control" accept="image/*">
                </div>
                <p class="form-help">Leave empty to keep current image</p>
            </div>
            
            <!-- Multiple Tool Logos Section -->
            <div class="tools-section">
                <h5>🛠️ Tool/Technology Logos</h5>
                
                <!-- Existing Logos -->
                <?php if (!empty($existing_logos)): ?>
                    <p class="form-help" style="margin-bottom: 0.75rem;">Current logos:</p>
                    <div id="existingLogosContainer">
                        <?php foreach ($existing_logos as $index => $logo): ?>
                            <div class="existing-logo" id="existing-logo-<?php echo $index; ?>">
                                <img src="/portofolio/assets/images/<?php echo htmlspecialchars($logo); ?>" alt="Logo">
                                <span class="info">✓ Logo #<?php echo $index + 1; ?></span>
                                <input type="hidden" name="kept_logos[]" value="<?php echo htmlspecialchars($logo); ?>">
                                <button type="button" class="btn-remove-logo" onclick="removeExisting(<?php echo $index; ?>)">×</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <p class="form-help" style="margin: 1rem 0 0.75rem;">Add new logos:</p>
                
                <div id="logoInputsContainer">
                    <div class="logo-input-group" data-index="0">
                        <span class="logo-number">+</span>
                        <input type="file" name="tool_logos[]" class="form-control" accept="image/*" onchange="previewLogo(this)">
                        <button type="button" class="btn-remove-logo" onclick="removeLogo(this)" style="display: none;">×</button>
                    </div>
                </div>
                
                <button type="button" class="btn-add-logo" onclick="addMoreLogos()">
                    <span class="plus-icon">+</span>
                    Add Another Logo
                </button>
            </div>
            
            <div class="form-group">
                <label for="description">Project Description</label>
                <textarea id="description" name="description" class="form-control" rows="5"><?php echo htmlspecialchars($project['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="github_link">GitHub Repository URL</label>
                <input type="url" id="github_link" name="github_link" class="form-control" value="<?php echo htmlspecialchars($project['project_link_github'] ?? ''); ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <span>💾</span> Update Project
                </button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
let logoIndex = 1;

function addMoreLogos() {
    const container = document.getElementById('logoInputsContainer');
    
    const newGroup = document.createElement('div');
    newGroup.className = 'logo-input-group';
    newGroup.dataset.index = logoIndex;
    
    newGroup.innerHTML = `
        <span class="logo-number">+</span>
        <input type="file" name="tool_logos[]" class="form-control" accept="image/*" onchange="previewLogo(this)">
        <button type="button" class="btn-remove-logo" onclick="removeLogo(this)">×</button>
    `;
    
    container.appendChild(newGroup);
    logoIndex++;
    updateRemoveButtons();
}

function removeLogo(button) {
    const group = button.closest('.logo-input-group');
    group.remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const groups = document.querySelectorAll('.logo-input-group');
    groups.forEach((group) => {
        const removeBtn = group.querySelector('.btn-remove-logo');
        removeBtn.style.display = groups.length > 1 ? 'flex' : 'none';
    });
}

function removeExisting(index) {
    const element = document.getElementById('existing-logo-' + index);
    if (element) {
        element.style.opacity = '0';
        element.style.transform = 'scale(0.95)';
        setTimeout(() => element.remove(), 200);
    }
}

function previewLogo(input) {
    // Optional: add preview functionality
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
