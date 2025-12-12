<?php
/**
 * Admin - Add Work Experience (Enhanced with Start/End Date, Status)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Add Experience';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company = $_POST['company'] ?? '';
    $date_work_start = $_POST['date_work_start'] ?? null;
    $date_work_end = $_POST['date_work_end'] ?? null;
    $still_working = isset($_POST['still_working']) ? 1 : 0;
    $work_status = $_POST['work_status'] ?? 'kerja';
    $description = $_POST['description'] ?? '';
    $images = [];
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    // Handle multiple file uploads
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
    
    // If still working, set end date to null
    if ($still_working) {
        $date_work_end = null;
    }
    
    $images_string = implode(',', $images);
    
    $stmt = $pdo->prepare("INSERT INTO work_experience_page (image_activity_work_carrousel, name_company, date_work_start, date_work_end, still_working, work_status, activity_description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$images_string, $company, $date_work_start ?: null, $date_work_end ?: null, $still_working, $work_status, $description]);
    header('Location: index.php?added=1');
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
    margin-top: 1rem;
}
.btn-add-more:hover {
    background: linear-gradient(135deg, #fff5f6 0%, #ffe8eb 100%);
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
.date-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
.still-working-check {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: #e8f5e9;
    border: 2px solid #4caf50;
    border-radius: 10px;
    margin-top: 0.5rem;
    cursor: pointer;
    transition: all 0.3s;
}
.still-working-check:hover {
    background: #c8e6c9;
}
.still-working-check input[type="checkbox"] {
    width: 20px;
    height: 20px;
    accent-color: #4caf50;
}
.still-working-check .check-label {
    color: #2e7d32;
    font-weight: 600;
}
.status-select {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
}
.status-option {
    flex: 1;
    padding: 1rem;
    border: 2px solid #ddd;
    border-radius: 10px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}
.status-option:hover {
    border-color: var(--accent);
}
.status-option.selected {
    border-color: var(--accent);
    background: linear-gradient(135deg, #fff5f6 0%, #ffe8eb 100%);
}
.status-option input {
    display: none;
}
.status-option .status-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}
.status-option .status-label {
    font-weight: 600;
    color: var(--primary);
}
</style>

<div class="form-card">
    <div class="form-card-header">
        <h3>💼 Add Work Experience</h3>
        <p>Add work experience with details and images</p>
    </div>
    
    <div class="form-card-body">
        <form method="POST" enctype="multipart/form-data" id="experienceForm">
            <div class="form-group">
                <label for="company">Company Name <span class="required">*</span></label>
                <input type="text" id="company" name="company" class="form-control" placeholder="e.g. PT. Example Indonesia" required>
            </div>
            
            <!-- Work Status -->
            <div class="form-group">
                <label>Status Pekerjaan</label>
                <div class="status-select">
                    <label class="status-option selected" id="status-kerja">
                        <input type="radio" name="work_status" value="kerja" checked onchange="updateStatus(this)">
                        <div class="status-icon">💼</div>
                        <div class="status-label">Kerja</div>
                    </label>
                    <label class="status-option" id="status-magang">
                        <input type="radio" name="work_status" value="magang" onchange="updateStatus(this)">
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
                        <input type="date" id="date_work_start" name="date_work_start" class="form-control">
                    </div>
                    <div id="endDateContainer">
                        <label for="date_work_end" style="font-size: 0.85rem; color: #666;">Tanggal Selesai</label>
                        <input type="date" id="date_work_end" name="date_work_end" class="form-control">
                    </div>
                </div>
                
                <!-- Still Working Checkbox -->
                <label class="still-working-check" id="stillWorkingLabel">
                    <input type="checkbox" name="still_working" id="still_working" onchange="toggleEndDate()">
                    <span class="check-label">✓ Masih bekerja disini?</span>
                </label>
            </div>
            
            <div class="form-group">
                <label for="description">Activity Description</label>
                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Describe your role and activities..."></textarea>
            </div>
            
            <!-- Carousel Images Section -->
            <div class="carousel-images-section">
                <h4>🖼️ Activity Images (Carousel)</h4>
                <p style="font-size: 0.9rem; color: #666; margin-bottom: 1rem;">Add multiple images that will display as a carousel</p>
                
                <div id="imageInputsContainer">
                    <div class="image-input-group" data-index="0">
                        <div class="group-header">
                            <span class="group-number">1</span>
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
                    <span>💾</span> Save Experience
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
        <input type="file" name="images[]" class="form-control" accept="image/*" onchange="previewImage(this)">
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
    document.querySelectorAll('.image-input-group').forEach((group, index) => {
        group.querySelector('.group-number').textContent = index + 1;
    });
}

function updateRemoveButtons() {
    const groups = document.querySelectorAll('.image-input-group');
    groups.forEach(group => {
        group.querySelector('.btn-remove-group').style.display = groups.length > 1 ? 'flex' : 'none';
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

function toggleEndDate() {
    const isChecked = document.getElementById('still_working').checked;
    const endDateContainer = document.getElementById('endDateContainer');
    const endDateInput = document.getElementById('date_work_end');
    
    if (isChecked) {
        endDateContainer.style.opacity = '0.5';
        endDateInput.disabled = true;
        endDateInput.value = '';
    } else {
        endDateContainer.style.opacity = '1';
        endDateInput.disabled = false;
    }
}

function updateStatus(radio) {
    document.querySelectorAll('.status-option').forEach(opt => opt.classList.remove('selected'));
    radio.closest('.status-option').classList.add('selected');
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
