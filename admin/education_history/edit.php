<?php
/**
 * Admin - Edit Education History
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Edit Education';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM education_history WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) { header('Location: index.php'); exit; }

$existing_images = [];
if (!empty($item['image_activity'])) {
    $existing_images = array_filter(array_map('trim', explode(',', $item['image_activity'])));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name_education'] ?? '';
    $description = $_POST['description'] ?? '';
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $still_studying = isset($_POST['still_studying']) ? 1 : 0;
    
    $kept_images = $_POST['kept_images'] ?? [];
    $images = $kept_images;
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    if (!empty($_FILES['images']['name'][0])) {
        $file_count = count($_FILES['images']['name']);
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                $filename = time() . '_edu_' . $i . '_' . basename($_FILES['images']['name'][$i]);
                if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_dir . $filename)) {
                    $images[] = $filename;
                }
            }
        }
    }
    
    if ($still_studying) $end_date = null;
    
    $images_string = implode(',', $images);
    
    $stmt = $pdo->prepare("UPDATE education_history SET name_education = ?, description = ?, image_activity = ?, start_date = ?, end_date = ?, still_studying = ? WHERE id = ?");
    $stmt->execute([$name, $description, $images_string, $start_date ?: null, $end_date ?: null, $still_studying, $id]);
    header('Location: index.php?updated=1');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

<style>
.image-input-group { background: #f8f9fa; border: 2px dashed #ddd; border-radius: 12px; padding: 1rem; margin-bottom: 0.75rem; }
.image-input-group .group-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
.image-input-group .group-number { background: var(--gradient-accent); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.75rem; }
.btn-remove-group { background: #e74c3c; color: white; border: none; width: 26px; height: 26px; border-radius: 50%; cursor: pointer; }
.btn-add-more { display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 1rem; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: 2px dashed var(--accent); border-radius: 12px; cursor: pointer; color: var(--accent); font-weight: 600; margin-top: 1rem; }
.btn-add-more .plus-icon { width: 28px; height: 28px; background: var(--gradient-accent); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
.existing-image { display: flex; align-items: center; gap: 1rem; background: #e8f4e8; border: 2px solid #27ae60; border-radius: 12px; padding: 1rem; margin-bottom: 0.75rem; }
.existing-image img { width: 80px; height: 60px; object-fit: cover; border-radius: 8px; }
.existing-image .info { flex: 1; font-size: 0.9rem; color: #27ae60; }
.date-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.still-check { display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: #e8f5e9; border: 2px solid #4caf50; border-radius: 10px; margin-top: 0.5rem; cursor: pointer; }
.carousel-section { background: #f0f4f8; border-radius: 12px; padding: 1.5rem; margin-top: 1rem; }
.carousel-section h5 { margin: 0 0 1rem 0; color: var(--primary); }
</style>

<div class="form-card">
    <div class="form-card-header">
        <h3>✏️ Edit Education History</h3>
    </div>
    
    <div class="form-card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name_education">Education Name <span class="required">*</span></label>
                <input type="text" id="name_education" name="name_education" class="form-control" value="<?php echo htmlspecialchars($item['name_education'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($item['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Period</label>
                <div class="date-row">
                    <div>
                        <label style="font-size: 0.85rem; color: #666;">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $item['start_date'] ?? ''; ?>">
                    </div>
                    <div id="endDateContainer" style="<?php echo ($item['still_studying'] ?? 0) ? 'opacity: 0.5;' : ''; ?>">
                        <label style="font-size: 0.85rem; color: #666;">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo $item['end_date'] ?? ''; ?>" <?php echo ($item['still_studying'] ?? 0) ? 'disabled' : ''; ?>>
                    </div>
                </div>
                <label class="still-check">
                    <input type="checkbox" name="still_studying" id="still_studying" onchange="toggleEndDate()" <?php echo ($item['still_studying'] ?? 0) ? 'checked' : ''; ?>>
                    <span style="color: #2e7d32; font-weight: 600;">✓ Masih belajar disini?</span>
                </label>
            </div>
            
            <div class="carousel-section">
                <h5>🖼️ Activity Images (Carousel)</h5>
                
                <?php if (!empty($existing_images)): ?>
                    <p class="form-help" style="margin-bottom: 0.75rem;">Current images:</p>
                    <div id="existingImagesContainer">
                        <?php foreach ($existing_images as $index => $image): ?>
                            <div class="existing-image" id="existing-<?php echo $index; ?>">
                                <img src="/portofolio/assets/images/<?php echo htmlspecialchars($image); ?>" alt="Image">
                                <span class="info">✓ Image #<?php echo $index + 1; ?></span>
                                <input type="hidden" name="kept_images[]" value="<?php echo htmlspecialchars($image); ?>">
                                <button type="button" class="btn-remove-group" onclick="removeExisting(<?php echo $index; ?>)">×</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <p class="form-help" style="margin: 1rem 0 0.75rem;">Add new images:</p>
                
                <div id="imageInputsContainer">
                    <div class="image-input-group">
                        <div class="group-header">
                            <span class="group-number">+</span>
                            <button type="button" class="btn-remove-group" onclick="removeGroup(this)" style="display: none;">×</button>
                        </div>
                        <input type="file" name="images[]" class="form-control" accept="image/*">
                    </div>
                </div>
                
                <button type="button" class="btn-add-more" onclick="addMoreImages()">
                    <span class="plus-icon">+</span>
                    Add Another Image
                </button>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit"><span>💾</span> Update Education</button>
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
        <div class="group-header"><span class="group-number">+</span><button type="button" class="btn-remove-group" onclick="removeGroup(this)">×</button></div>
        <input type="file" name="images[]" class="form-control" accept="image/*">
    `;
    container.appendChild(newGroup);
    imageIndex++;
    updateRemoveButtons();
}
function removeGroup(btn) { btn.closest('.image-input-group').remove(); updateRemoveButtons(); }
function updateRemoveButtons() {
    const groups = document.querySelectorAll('.image-input-group');
    groups.forEach(g => g.querySelector('.btn-remove-group').style.display = groups.length > 1 ? 'flex' : 'none');
}
function removeExisting(index) {
    const el = document.getElementById('existing-' + index);
    if (el) { el.style.opacity = '0'; setTimeout(() => el.remove(), 200); }
}
function toggleEndDate() {
    const isChecked = document.getElementById('still_studying').checked;
    const container = document.getElementById('endDateContainer');
    const input = document.getElementById('end_date');
    container.style.opacity = isChecked ? '0.5' : '1';
    input.disabled = isChecked;
    if (isChecked) input.value = '';
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
