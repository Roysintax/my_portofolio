<?php
/**
 * Admin - Add Design (Carousel - Multiple Images per Design)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Add Design';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $design_link = $_POST['design_link'] ?? '';
    $images = [];
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    // Handle multiple file uploads for carousel
    if (!empty($_FILES['design_images']['name'][0])) {
        $file_count = count($_FILES['design_images']['name']);
        
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['design_images']['error'][$i] === UPLOAD_ERR_OK) {
                $filename = time() . '_design_' . $i . '_' . basename($_FILES['design_images']['name'][$i]);
                if (move_uploaded_file($_FILES['design_images']['tmp_name'][$i], $target_dir . $filename)) {
                    $images[] = $filename;
                }
            }
        }
    }
    
    // Store images as comma-separated string
    $images_string = implode(',', $images);
    
    $stmt = $pdo->prepare("INSERT INTO design_page (title, design_image, design_link) VALUES (?, ?, ?)");
    if ($stmt->execute([$title, $images_string, $design_link])) {
        header('Location: index.php?added=1');
        exit;
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
.image-input-group:hover { border-color: var(--accent); background: #fff5f6; }
.image-input-group .group-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.image-input-group .group-number { background: var(--gradient-accent); color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.85rem; }
.btn-remove-group { background: #e74c3c; color: white; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; }
.btn-add-more { display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 1rem; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: 2px dashed var(--accent); border-radius: 12px; cursor: pointer; color: var(--accent); font-weight: 600; margin-top: 1rem; }
.btn-add-more:hover { background: linear-gradient(135deg, #fff5f6 0%, #ffe8eb 100%); }
.btn-add-more .plus-icon { width: 32px; height: 32px; background: var(--gradient-accent); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
.image-preview { max-width: 120px; max-height: 80px; border-radius: 8px; margin-top: 0.5rem; display: none; }
.carousel-section { background: #f0f4f8; border-radius: 12px; padding: 1.5rem; margin-top: 1rem; }
.carousel-section h5 { margin: 0 0 1rem 0; color: var(--primary); font-size: 0.95rem; }
</style>

<div class="form-card">
    <div class="form-card-header">
        <h3>🎨 Add New Design</h3>
        <p>Create a design with carousel images</p>
    </div>
    
    <div class="form-card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Design Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Mobile App UI Design" required>
            </div>
            
            <div class="form-group">
                <label for="design_link">Design Link (Optional)</label>
                <input type="url" id="design_link" name="design_link" class="form-control" placeholder="https://dribbble.com/...">
            </div>
            
            <!-- Carousel Images Section -->
            <div class="carousel-section">
                <h5>🖼️ Design Images (Carousel)</h5>
                <p class="form-help" style="margin-bottom: 1rem;">Tambahkan gambar desain. <strong>Ukuran rekomendasi: 1920x1080 px (16:9)</strong> atau <strong>1080x1080 px (1:1)</strong></p>
                
                <div id="imageInputsContainer">
                    <div class="image-input-group" data-index="0">
                        <div class="group-header">
                            <span class="group-number">1</span>
                            <button type="button" class="btn-remove-group" onclick="removeGroup(this)" style="display: none;">×</button>
                        </div>
                        <input type="file" name="design_images[]" class="form-control" accept="image/*" required onchange="previewImage(this)">
                        <img class="image-preview" src="" alt="Preview">
                    </div>
                </div>
                
                <button type="button" class="btn-add-more" onclick="addMoreImages()">
                    <span class="plus-icon">+</span>
                    Add Another Image
                </button>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <span>💾</span> Save Design
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
        <input type="file" name="design_images[]" class="form-control" accept="image/*" onchange="previewImage(this)">
        <img class="image-preview" src="" alt="Preview">
    `;
    
    container.appendChild(newGroup);
    imageIndex++;
    updateGroupNumbers();
    updateRemoveButtons();
    newGroup.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function removeGroup(button) {
    button.closest('.image-input-group').remove();
    updateGroupNumbers();
    updateRemoveButtons();
}

function updateGroupNumbers() {
    document.querySelectorAll('.image-input-group').forEach((g, i) => {
        g.querySelector('.group-number').textContent = i + 1;
    });
}

function updateRemoveButtons() {
    const groups = document.querySelectorAll('.image-input-group');
    groups.forEach(g => {
        g.querySelector('.btn-remove-group').style.display = groups.length > 1 ? 'flex' : 'none';
    });
}

function previewImage(input) {
    const preview = input.parentElement.querySelector('.image-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
