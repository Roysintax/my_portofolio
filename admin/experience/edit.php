<?php
/**
 * Admin - Edit Work Experience (Enhanced with Start/End Date, Status)
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
    $date_work_start = $_POST['date_work_start'] ?? null;
    $date_work_end = $_POST['date_work_end'] ?? null;
    $still_working = isset($_POST['still_working']) ? 1 : 0;
    $work_status = $_POST['work_status'] ?? 'kerja';
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
    
    if ($still_working) {
        $date_work_end = null;
    }
    
    $images_string = implode(',', $images);
    
    $stmt = $pdo->prepare("UPDATE work_experience_page SET image_activity_work_carrousel = ?, name_company = ?, date_work_start = ?, date_work_end = ?, still_working = ?, work_status = ?, activity_description = ? WHERE id = ?");
    $stmt->execute([$images_string, $company, $date_work_start ?: null, $date_work_end ?: null, $still_working, $work_status, $description, $id]);
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
}
.image-input-group:hover { border-color: var(--accent); background: #fff5f6; }
.image-input-group .group-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.image-input-group .group-number { background: var(--gradient-accent); color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; }
.btn-remove-group { background: #e74c3c; color: white; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; }
.btn-add-more { display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 1rem; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: 2px dashed var(--accent); border-radius: 12px; cursor: pointer; color: var(--accent); font-weight: 600; margin-top: 1rem; }
.btn-add-more .plus-icon { width: 32px; height: 32px; background: var(--gradient-accent); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
.carousel-images-section { background: #f0f4f8; border-radius: 12px; padding: 1.5rem; margin-top: 1rem; }
.carousel-images-section h4 { margin-bottom: 1rem; color: var(--primary); }
.existing-image { display: flex; align-items: center; gap: 1rem; background: #e8f4e8; border: 2px solid #27ae60; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; }
.existing-image img { width: 80px; height: 60px; object-fit: cover; border-radius: 8px; }
.existing-image .info { flex: 1; font-size: 0.9rem; color: #27ae60; }
.date-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.still-working-check { display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: #e8f5e9; border: 2px solid #4caf50; border-radius: 10px; margin-top: 0.5rem; cursor: pointer; }
.still-working-check input[type="checkbox"] { width: 20px; height: 20px; accent-color: #4caf50; }
.still-working-check .check-label { color: #2e7d32; font-weight: 600; }
.status-select { display: flex; gap: 1rem; margin-top: 0.5rem; }
.status-option { flex: 1; padding: 1rem; border: 2px solid #ddd; border-radius: 10px; text-align: center; cursor: pointer; }
.status-option.selected { border-color: var(--accent); background: linear-gradient(135deg, #fff5f6 0%, #ffe8eb 100%); }
.status-option input { display: none; }
.status-option .status-icon { font-size: 2rem; margin-bottom: 0.5rem; }
.status-option .status-label { font-weight: 600; color: var(--primary); }
</style>

<div class="form-card">
    <div class="form-card-header">
        <h3>✏️ Edit Work Experience</h3>
        <p>Update work experience details</p>
    </div>
    
    <div class="form-card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="company">Company Name <span class="required">*</span></label>
                <input type="text" id="company" name="company" class="form-control" value="<?php echo htmlspecialchars($item['name_company'] ?? ''); ?>" required>
            </div>
            
            <!-- Work Status -->
            <div class="form-group">
                <label>Status Pekerjaan</label>
                <div class="status-select">
                    <label class="status-option <?php echo ($item['work_status'] ?? 'kerja') === 'kerja' ? 'selected' : ''; ?>">
                        <input type="radio" name="work_status" value="kerja" <?php echo ($item['work_status'] ?? 'kerja') === 'kerja' ? 'checked' : ''; ?> onchange="updateStatus(this)">
                        <div class="status-icon">💼</div>
                        <div class="status-label">Kerja</div>
                    </label>
                    <label class="status-option <?php echo ($item['work_status'] ?? '') === 'magang' ? 'selected' : ''; ?>">
                        <input type="radio" name="work_status" value="magang" <?php echo ($item['work_status'] ?? '') === 'magang' ? 'checked' : ''; ?> onchange="updateStatus(this)">
                        <div class="status-icon">📚</div>
                        <div class="status-label">Magang</div>
                    </label>
                </div>
            </div>
            
            <!-- Date Range -->
            <div class="form-group">
                <label>Periode Kerja</label>
                <div class="date-row">
                    <div>
                        <label for="date_work_start" style="font-size: 0.85rem; color: #666;">Tanggal Mulai</label>
                        <input type="date" id="date_work_start" name="date_work_start" class="form-control" value="<?php echo $item['date_work_start'] ?? ''; ?>">
                    </div>
                    <div id="endDateContainer" style="<?php echo ($item['still_working'] ?? 0) ? 'opacity: 0.5;' : ''; ?>">
                        <label for="date_work_end" style="font-size: 0.85rem; color: #666;">Tanggal Selesai</label>
                        <input type="date" id="date_work_end" name="date_work_end" class="form-control" value="<?php echo $item['date_work_end'] ?? ''; ?>" <?php echo ($item['still_working'] ?? 0) ? 'disabled' : ''; ?>>
                    </div>
                </div>
                
                <label class="still-working-check">
                    <input type="checkbox" name="still_working" id="still_working" onchange="toggleEndDate()" <?php echo ($item['still_working'] ?? 0) ? 'checked' : ''; ?>>
                    <span class="check-label">✓ Masih bekerja disini?</span>
                </label>
            </div>
            
            <div class="form-group">
                <label for="description">Activity Description</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($item['activity_description'] ?? ''); ?></textarea>
            </div>
            
            <!-- Carousel Images Section -->
            <div class="carousel-images-section">
                <h4>🖼️ Activity Images (Carousel)</h4>
                
                <?php if (!empty($existing_images)): ?>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 1rem;">Current images:</p>
                    <div id="existingImagesContainer">
                        <?php foreach ($existing_images as $index => $image): ?>
                            <div class="existing-image" id="existing-<?php echo $index; ?>">
                                <img src="/portofolio/assets/images/<?php echo htmlspecialchars($image); ?>" alt="Current">
                                <span class="info">✓ Saved Image #<?php echo $index + 1; ?></span>
                                <input type="hidden" name="kept_images[]" value="<?php echo htmlspecialchars($image); ?>">
                                <button type="button" class="btn-remove-group" onclick="removeExisting(<?php echo $index; ?>)">×</button>
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
                        <input type="file" name="images[]" class="form-control" accept="image/*">
                    </div>
                </div>
                
                <button type="button" class="btn-add-more" onclick="addMoreImages()">
                    <span class="plus-icon">+</span>
                    Add Another Image
                </button>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit"><span>💾</span> Update Experience</button>
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
        <input type="file" name="images[]" class="form-control" accept="image/*">
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
function toggleEndDate() {
    const isChecked = document.getElementById('still_working').checked;
    const container = document.getElementById('endDateContainer');
    const input = document.getElementById('date_work_end');
    container.style.opacity = isChecked ? '0.5' : '1';
    input.disabled = isChecked;
    if (isChecked) input.value = '';
}
function updateStatus(radio) {
    document.querySelectorAll('.status-option').forEach(opt => opt.classList.remove('selected'));
    radio.closest('.status-option').classList.add('selected');
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
