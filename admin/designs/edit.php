<?php
/**
 * Admin - Edit Design (Carousel - Multiple Images)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Edit Design';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM design_page WHERE id = ?");
$stmt->execute([$id]);
$design = $stmt->fetch();

if (!$design) {
    header('Location: index.php');
    exit;
}

// Parse existing images
$existing_images = [];
if (!empty($design['design_image'])) {
    $existing_images = array_filter(array_map('trim', explode(',', $design['design_image'])));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $design_link = $_POST['design_link'] ?? '';
    
    // Keep existing images that weren't removed
    $kept_images = $_POST['kept_images'] ?? [];
    $images = $kept_images;
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    // Handle new file uploads
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
    
    $images_string = implode(',', $images);
    
    $stmt = $pdo->prepare("UPDATE design_page SET title = ?, design_image = ?, design_link = ? WHERE id = ?");
    $stmt->execute([$title, $images_string, $design_link, $id]);
    header('Location: index.php?updated=1');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

<style>
.image-input-group { background: #f8f9fa; border: 2px dashed #ddd; border-radius: 12px; padding: 1rem; margin-bottom: 0.75rem; }
.image-input-group:hover { border-color: var(--accent); background: #fff5f6; }
.image-input-group .group-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
.image-input-group .group-number { background: var(--gradient-accent); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.75rem; }
.btn-remove-group { background: #e74c3c; color: white; border: none; width: 26px; height: 26px; border-radius: 50%; cursor: pointer; }
.btn-add-more { display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 1rem; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: 2px dashed var(--accent); border-radius: 12px; cursor: pointer; color: var(--accent); font-weight: 600; margin-top: 1rem; }
.btn-add-more .plus-icon { width: 28px; height: 28px; background: var(--gradient-accent); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
.existing-image { display: flex; align-items: center; gap: 1rem; background: #e8f4e8; border: 2px solid #27ae60; border-radius: 12px; padding: 1rem; margin-bottom: 0.75rem; }
.existing-image img { width: 80px; height: 60px; object-fit: cover; border-radius: 8px; }
.existing-image .info { flex: 1; font-size: 0.9rem; color: #27ae60; }
.carousel-section { background: #f0f4f8; border-radius: 12px; padding: 1.5rem; margin-top: 1rem; }
.carousel-section h5 { margin: 0 0 1rem 0; color: var(--primary); font-size: 0.95rem; }
</style>

<div class="form-card">
    <div class="form-card-header">
        <h3>✏️ Edit Design</h3>
        <p>Update design with carousel images</p>
    </div>
    
    <div class="form-card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Design Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($design['title'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="design_link">Design Link</label>
                <input type="url" id="design_link" name="design_link" class="form-control" value="<?php echo htmlspecialchars($design['design_link'] ?? ''); ?>">
            </div>
            
            <!-- Carousel Images Section -->
            <div class="carousel-section">
                <h5>🖼️ Design Images (Carousel)</h5>
                
                <?php if (!empty($existing_images)): ?>
                    <p class="form-help" style="margin-bottom: 0.75rem;">Current images:</p>
                    <div id="existingImagesContainer">
                        <?php foreach ($existing_images as $index => $image): ?>
                            <div class="existing-image" id="existing-<?php echo $index; ?>">
                                <img src="/portofolio/assets/images/<?php echo htmlspecialchars($image); ?>" alt="Design">
                                <span class="info">✓ Image #<?php echo $index + 1; ?></span>
                                <input type="hidden" name="kept_images[]" value="<?php echo htmlspecialchars($image); ?>">
                                <button type="button" class="btn-remove-group" onclick="removeExisting(<?php echo $index; ?>)">×</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <p class="form-help" style="margin: 1rem 0 0.75rem;">Add new images:</p>
                
                <div id="imageInputsContainer">
                    <div class="image-input-group" data-index="0">
                        <div class="group-header">
                            <span class="group-number">+</span>
                            <button type="button" class="btn-remove-group" onclick="removeGroup(this)" style="display: none;">×</button>
                        </div>
                        <input type="file" name="design_images[]" class="form-control" accept="image/*">
                    </div>
                </div>
                
                <button type="button" class="btn-add-more" onclick="addMoreImages()">
                    <span class="plus-icon">+</span>
                    Add Another Image
                </button>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit"><span>💾</span> Update Design</button>
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
    newGroup.innerHTML = `
        <div class="group-header">
            <span class="group-number">+</span>
            <button type="button" class="btn-remove-group" onclick="removeGroup(this)">×</button>
        </div>
        <input type="file" name="design_images[]" class="form-control" accept="image/*">
    `;
    container.appendChild(newGroup);
    imageIndex++;
    updateRemoveButtons();
}

function removeGroup(button) { button.closest('.image-input-group').remove(); updateRemoveButtons(); }
function updateRemoveButtons() {
    const groups = document.querySelectorAll('.image-input-group');
    groups.forEach(g => { g.querySelector('.btn-remove-group').style.display = groups.length > 1 ? 'flex' : 'none'; });
}
function removeExisting(index) {
    const el = document.getElementById('existing-' + index);
    if (el) { el.style.opacity = '0'; setTimeout(() => el.remove(), 200); }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
