<?php
/**
 * Admin - Add Design (Multiple Images)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Add Design';
$success_count = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $design_links = $_POST['design_link'] ?? [];
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    // Handle multiple file uploads
    if (!empty($_FILES['design_images']['name'][0])) {
        $file_count = count($_FILES['design_images']['name']);
        
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['design_images']['error'][$i] === UPLOAD_ERR_OK) {
                $filename = time() . '_' . $i . '_' . basename($_FILES['design_images']['name'][$i]);
                
                if (move_uploaded_file($_FILES['design_images']['tmp_name'][$i], $target_dir . $filename)) {
                    $design_link = $design_links[$i] ?? '';
                    
                    $stmt = $pdo->prepare("INSERT INTO design_page (design_image, design_link) VALUES (?, ?)");
                    if ($stmt->execute([$filename, $design_link])) {
                        $success_count++;
                    }
                }
            }
        }
        
        if ($success_count > 0) {
            header('Location: index.php?added=' . $success_count);
            exit;
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<style>
.image-input-group {
    background: #f8f9fa;
    border: 2px dashed #ddd;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    position: relative;
    transition: all 0.3s ease;
}

.image-input-group:hover {
    border-color: var(--accent);
    background: #fff5f6;
}

.image-input-group .group-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.image-input-group .group-number {
    background: var(--gradient-accent);
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.85rem;
}

.btn-remove-group {
    background: #e74c3c;
    color: white;
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-remove-group:hover {
    background: #c0392b;
    transform: scale(1.1);
}

.btn-add-more {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px dashed var(--accent);
    border-radius: 12px;
    cursor: pointer;
    color: var(--accent);
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    margin-top: 1rem;
}

.btn-add-more:hover {
    background: linear-gradient(135deg, #fff5f6 0%, #ffe8eb 100%);
    transform: translateY(-2px);
}

.btn-add-more .plus-icon {
    width: 32px;
    height: 32px;
    background: var(--gradient-accent);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 300;
}

.image-preview {
    max-width: 150px;
    max-height: 100px;
    border-radius: 8px;
    margin-top: 0.5rem;
    display: none;
}

.form-row-inner {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

@media (max-width: 600px) {
    .form-row-inner {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="form-card">
    <div class="form-card-header">
        <h3>🎨 Add New Designs</h3>
        <p>Upload one or more design images</p>
    </div>
    
    <div class="form-card-body">
        <form method="POST" enctype="multipart/form-data" id="designForm">
            <div id="imageInputsContainer">
                <!-- Initial image input group -->
                <div class="image-input-group" data-index="0">
                    <div class="group-header">
                        <span class="group-number">1</span>
                        <button type="button" class="btn-remove-group" onclick="removeGroup(this)" style="display: none;">×</button>
                    </div>
                    
                    <div class="form-row-inner">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Design Image <span class="required">*</span></label>
                            <input type="file" name="design_images[]" class="form-control" accept="image/*" required onchange="previewImage(this)">
                            <img class="image-preview" src="" alt="Preview">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Design Link (Optional)</label>
                            <input type="url" name="design_link[]" class="form-control" placeholder="https://dribbble.com/...">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Add More Button -->
            <button type="button" class="btn-add-more" onclick="addMoreImages()">
                <span class="plus-icon">+</span>
                Add Another Image
            </button>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <span>💾</span> Save All Designs
                </button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
let imageIndex = 1;

function addMoreImages() {
    const container = document.getElementById('imageInputsContainer');
    
    const newGroup = document.createElement('div');
    newGroup.className = 'image-input-group';
    newGroup.dataset.index = imageIndex;
    
    newGroup.innerHTML = `
        <div class="group-header">
            <span class="group-number">${imageIndex + 1}</span>
            <button type="button" class="btn-remove-group" onclick="removeGroup(this)">×</button>
        </div>
        
        <div class="form-row-inner">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Design Image <span class="required">*</span></label>
                <input type="file" name="design_images[]" class="form-control" accept="image/*" required onchange="previewImage(this)">
                <img class="image-preview" src="" alt="Preview">
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label>Design Link (Optional)</label>
                <input type="url" name="design_link[]" class="form-control" placeholder="https://dribbble.com/...">
            </div>
        </div>
    `;
    
    container.appendChild(newGroup);
    imageIndex++;
    
    updateGroupNumbers();
    updateRemoveButtons();
    
    // Scroll to new group
    newGroup.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function removeGroup(button) {
    const group = button.closest('.image-input-group');
    group.style.opacity = '0';
    group.style.transform = 'scale(0.9)';
    
    setTimeout(() => {
        group.remove();
        updateGroupNumbers();
        updateRemoveButtons();
    }, 200);
}

function updateGroupNumbers() {
    const groups = document.querySelectorAll('.image-input-group');
    groups.forEach((group, index) => {
        group.querySelector('.group-number').textContent = index + 1;
    });
}

function updateRemoveButtons() {
    const groups = document.querySelectorAll('.image-input-group');
    groups.forEach((group, index) => {
        const removeBtn = group.querySelector('.btn-remove-group');
        // Show remove button only if more than 1 group
        removeBtn.style.display = groups.length > 1 ? 'flex' : 'none';
    });
}

function previewImage(input) {
    const preview = input.parentElement.querySelector('.image-preview');
    
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
