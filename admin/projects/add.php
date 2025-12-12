<?php
/**
 * Admin - Add Project (with Multiple Tool Logos)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Add Project';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $github_link = $_POST['github_link'] ?? '';
    $project_image = '';
    $tool_logos = [];
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    // Handle project image upload
    if (!empty($_FILES['project_image']['name'])) {
        $filename = time() . '_' . basename($_FILES['project_image']['name']);
        if (move_uploaded_file($_FILES['project_image']['tmp_name'], $target_dir . $filename)) {
            $project_image = $filename;
        }
    }
    
    // Handle multiple tool logo uploads
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
    
    // Store logos as comma-separated string
    $tool_logo_string = implode(',', $tool_logos);
    
    $stmt = $pdo->prepare("INSERT INTO project_page (title, project_image, tool_logo, description, project_link_github) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$title, $project_image, $tool_logo_string, $description, $github_link])) {
        header('Location: index.php?added=1');
        exit;
    } else {
        $error = 'Failed to add project';
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

.logo-input-group input[type="file"] {
    flex: 1;
}

.logo-input-group .btn-remove-logo {
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

.logo-input-group .btn-remove-logo:hover {
    background: #c0392b;
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
    display: none;
    flex-shrink: 0;
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
        <h3>✨ Add New Project</h3>
        <p>Create a new project entry for your portfolio</p>
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
                <label for="title">
                    Project Title <span class="required">*</span>
                </label>
                <input type="text" id="title" name="title" class="form-control" placeholder="e.g. E-Commerce Website" required>
            </div>
            
            <div class="form-group">
                <label for="project_image">
                    Project Image <span class="required">*</span>
                </label>
                <div class="file-upload-wrapper">
                    <input type="file" id="project_image" name="project_image" class="form-control" accept="image/*">
                </div>
                <p class="form-help">Recommended: 1200x800px, JPG or PNG format</p>
            </div>
            
            <!-- Multiple Tool Logos Section -->
            <div class="tools-section">
                <h5>🛠️ Tool/Technology Logos</h5>
                <p class="form-help" style="margin-bottom: 1rem;">Add logos of technologies used in this project</p>
                
                <div id="logoInputsContainer">
                    <div class="logo-input-group" data-index="0">
                        <span class="logo-number">1</span>
                        <img class="logo-preview" src="" alt="Preview">
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
                <label for="description">
                    Project Description <span class="required">*</span>
                </label>
                <textarea id="description" name="description" class="form-control" rows="5" placeholder="Describe your project, what it does, and the technologies used..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="github_link">GitHub Repository URL</label>
                <input type="url" id="github_link" name="github_link" class="form-control" placeholder="https://github.com/username/repository">
                <p class="form-help">Link to your project's source code repository</p>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <span>💾</span> Save Project
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
        <span class="logo-number">${logoIndex + 1}</span>
        <img class="logo-preview" src="" alt="Preview">
        <input type="file" name="tool_logos[]" class="form-control" accept="image/*" onchange="previewLogo(this)">
        <button type="button" class="btn-remove-logo" onclick="removeLogo(this)">×</button>
    `;
    
    container.appendChild(newGroup);
    logoIndex++;
    
    updateLogoNumbers();
    updateRemoveButtons();
}

function removeLogo(button) {
    const group = button.closest('.logo-input-group');
    group.style.opacity = '0';
    group.style.transform = 'scale(0.95)';
    
    setTimeout(() => {
        group.remove();
        updateLogoNumbers();
        updateRemoveButtons();
    }, 200);
}

function updateLogoNumbers() {
    const groups = document.querySelectorAll('.logo-input-group');
    groups.forEach((group, index) => {
        group.querySelector('.logo-number').textContent = index + 1;
    });
}

function updateRemoveButtons() {
    const groups = document.querySelectorAll('.logo-input-group');
    groups.forEach((group) => {
        const removeBtn = group.querySelector('.btn-remove-logo');
        removeBtn.style.display = groups.length > 1 ? 'flex' : 'none';
    });
}

function previewLogo(input) {
    const preview = input.parentElement.querySelector('.logo-preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
