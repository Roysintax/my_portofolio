<?php
/**
 * Admin - Edit Work Experience (with Multiple Image Carousel)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Edit Experience';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM work_experience_page WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    header('Location: index.php');
    exit;
}

// Parse existing images
$existing_images = [];
if (!empty($item['image_activity_work_carrousel'])) {
    $existing_images = array_filter(array_map('trim', explode(',', $item['image_activity_work_carrousel'])));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company = $_POST['company'] ?? '';
    $date_work = $_POST['date_work'] ?? null;
    $description = $_POST['description'] ?? '';
    
    // Keep existing images that weren't removed
    $kept_images = $_POST['kept_images'] ?? [];
    $images = $kept_images;
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    // Handle new file uploads
    if (!empty($_FILES['images']['name'][0])) {
        $file_count = count($_FILES['images']['name']);
        
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                $filename = time() . '_exp_' . $i . '_' . basename($_FILES['images']['name'][$i]);
                
                if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_dir . $filename)) {
                    $images[] = $filename;
                }
            }
        }
    }
    
    $images_string = implode(',', $images);
    
    $stmt = $pdo->prepare("UPDATE work_experience_page SET image_activity_work_carrousel = ?, name_company = ?, date_work = ?, activity_description = ? WHERE id = ?");
    $stmt->execute([$images_string, $company, $date_work ?: null, $description, $id]);
    header('Location: index.php?updated=1');
    exit;
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

.existing-image {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #e8f4e8;
    border: 2px solid #27ae60;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.existing-image img {
    width: 80px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.existing-image .info {
    flex: 1;
    font-size: 0.9rem;
    color: #27ae60;
}

.existing-image .btn-remove-existing {
    background: #e74c3c;
    color: white;
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1rem;
}

.carousel-images-section {
    background: #f0f4f8;
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 1rem;
}

.carousel-images-section h4 {
    margin-bottom: 1rem;
    color: var(--primary);
}

.image-preview {
    max-width: 120px;
    max-height: 80px;
    border-radius: 8px;
    margin-top: 0.5rem;
    display: none;
}
</style>

<div class="form-card">
    <div class="form-card-header">
        <h3>✏️ Edit Work Experience</h3>
        <p>Update work experience and manage carousel images</p>
    </div>
    
    <div class="form-card-body">
        <form method="POST" enctype="multipart/form-data" id="experienceForm">
            <div class="form-group">
                <label for="company">Company Name <span class="required">*</span></label>
                <input type="text" id="company" name="company" class="form-control" value="<?php echo htmlspecialchars($item['name_company'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="date_work">Work Date</label>
                <input type="date" id="date_work" name="date_work" class="form-control" value="<?php echo $item['date_work'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Activity Description</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($item['activity_description'] ?? ''); ?></textarea>
            </div>
            
            <!-- Carousel Images Section -->
            <div class="carousel-images-section">
                <h4>🖼️ Activity Images (Carousel)</h4>
                
                <!-- Existing Images -->
                <?php if (!empty($existing_images)): ?>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 1rem;">Current images:</p>
                    <div id="existingImagesContainer">
                        <?php foreach ($existing_images as $index => $image): ?>
                            <div class="existing-image" id="existing-<?php echo $index; ?>">
                                <img src="/portofolio/assets/images/<?php echo htmlspecialchars($image); ?>" alt="Current">
                                <span class="info">✓ Saved Image #<?php echo $index + 1; ?></span>
                                <input type="hidden" name="kept_images[]" value="<?php echo htmlspecialchars($image); ?>">
                                <button type="button" class="btn-remove-existing" onclick="removeExisting(<?php echo $index; ?>)">×</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <p style="font-size: 0.9rem; color: #666; margin: 1rem 0;">Add new images:</p>
                
                <div id="imageInputsContainer">
                    <div class="image-input-group" data-index="0">
                        <div class="group-header">
                            <span class="group-number">+</span>
                            <button type="button" class="btn-remove-group" onclick="removeGroup(this)" style="display: none;">×</button>
                        </div>
                        <input type="file" name="images[]" class="form-control" accept="image/*" onchange="previewImage(this)">
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
                    <span>💾</span> Update Experience
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
            <span class="group-number">+</span>
            <button type="button" class="btn-remove-group" onclick="removeGroup(this)">×</button>
        </div>
        <input type="file" name="images[]" class="form-control" accept="image/*" onchange="previewImage(this)">
        <img class="image-preview" src="" alt="Preview">
    `;
    
    container.appendChild(newGroup);
    imageIndex++;
    updateRemoveButtons();
    
    newGroup.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function removeGroup(button) {
    const group = button.closest('.image-input-group');
    group.remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const groups = document.querySelectorAll('.image-input-group');
    groups.forEach((group) => {
        const removeBtn = group.querySelector('.btn-remove-group');
        removeBtn.style.display = groups.length > 1 ? 'flex' : 'none';
    });
}

function removeExisting(index) {
    const element = document.getElementById('existing-' + index);
    if (element) {
        element.style.opacity = '0';
        element.style.transform = 'scale(0.9)';
        setTimeout(() => {
            element.remove();
        }, 200);
    }
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
