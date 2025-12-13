<?php
/**
 * Admin - Add Education History
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Add Education';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name_education'] ?? '';
    $description = $_POST['description'] ?? '';
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $still_studying = isset($_POST['still_studying']) ? 1 : 0;
    $images = [];
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    // Handle multiple image uploads for carousel
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
    
    $stmt = $pdo->prepare("INSERT INTO education_history (name_education, description, image_activity, start_date, end_date, still_studying) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $description, $images_string, $start_date ?: null, $end_date ?: null, $still_studying])) {
        header('Location: index.php?added=1');
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<style>
.image-input-group { background: #f8f9fa; border: 2px dashed #ddd; border-radius: 12px; padding: 1.5rem; margin-bottom: 1rem; }
.image-input-group:hover { border-color: var(--accent); background: #fff5f6; }
.image-input-group .group-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.image-input-group .group-number { background: var(--gradient-accent); color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; }
.btn-remove-group { background: #e74c3c; color: white; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; }
.btn-add-more { display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 1rem; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border: 2px dashed var(--accent); border-radius: 12px; cursor: pointer; color: var(--accent); font-weight: 600; margin-top: 1rem; }
.btn-add-more .plus-icon { width: 32px; height: 32px; background: var(--gradient-accent); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
.date-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.still-check { display: flex; align-items: center; gap: 0.75rem; padding: 1rem; background: #e8f5e9; border: 2px solid #4caf50; border-radius: 10px; margin-top: 0.5rem; cursor: pointer; }
.still-check input[type="checkbox"] { width: 20px; height: 20px; accent-color: #4caf50; }
.carousel-section { background: #f0f4f8; border-radius: 12px; padding: 1.5rem; margin-top: 1rem; }
.carousel-section h5 { margin: 0 0 1rem 0; color: var(--primary); }
</style>

<div class="form-card">
    <div class="form-card-header">
        <h3>📚 Add Education History</h3>
        <p>Add your educational background</p>
    </div>
    
    <div class="form-card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name_education">Education Name <span class="required">*</span></label>
                <input type="text" id="name_education" name="name_education" class="form-control" placeholder="e.g. Bachelor of Computer Science - University Name" required>
                <p class="form-help">📝 Nama pendidikan dan institusi (contoh: S1 Teknik Informatika - Universitas XYZ)</p>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Describe your education, achievements, activities..."></textarea>
                <p class="form-help">📋 Deskripsi kegiatan, prestasi, atau aktivitas selama pendidikan</p>
            </div>
            
            <div class="form-group">
                <label>Period</label>
                <div class="date-row">
                    <div>
                        <label style="font-size: 0.85rem; color: #666;">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control">
                    </div>
                    <div id="endDateContainer">
                        <label style="font-size: 0.85rem; color: #666;">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control">
                    </div>
                </div>
                <label class="still-check">
                    <input type="checkbox" name="still_studying" id="still_studying" onchange="toggleEndDate()">
                    <span style="color: #2e7d32; font-weight: 600;">✓ Masih belajar disini?</span>
                </label>
            </div>
            
            <!-- Activity Images Carousel -->
            <div class="carousel-section">
                <h5>🖼️ Activity Images (Carousel)</h5>
                <p class="form-help" style="margin-bottom: 1rem;">Tambahkan foto kegiatan pendidikan. <strong>Ukuran rekomendasi: 1920x1080 px (16:9)</strong></p>
                
                <div id="imageInputsContainer">
                    <div class="image-input-group" data-index="0">
                        <div class="group-header">
                            <span class="group-number">1</span>
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
                <button type="submit" class="btn-submit"><span>💾</span> Save Education</button>
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
            <span class="group-number">${imageIndex + 1}</span>
            <button type="button" class="btn-remove-group" onclick="removeGroup(this)">×</button>
        </div>
        <input type="file" name="images[]" class="form-control" accept="image/*">
    `;
    container.appendChild(newGroup);
    imageIndex++;
    updateRemoveButtons();
}
function removeGroup(btn) { btn.closest('.image-input-group').remove(); updateGroupNumbers(); updateRemoveButtons(); }
function updateGroupNumbers() { document.querySelectorAll('.image-input-group').forEach((g, i) => g.querySelector('.group-number').textContent = i + 1); }
function updateRemoveButtons() {
    const groups = document.querySelectorAll('.image-input-group');
    groups.forEach(g => g.querySelector('.btn-remove-group').style.display = groups.length > 1 ? 'flex' : 'none');
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
