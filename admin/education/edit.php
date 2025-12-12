<?php
/**
 * Admin - Edit Education/Certification (with Multiple Images Carousel)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Edit Education/Cert';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM education_and_certification_page WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    header('Location: index.php');
    exit;
}

// Parse existing activity images
$existing_images = [];
if (!empty($item['image_activity'])) {
    $existing_images = array_filter(array_map('trim', explode(',', $item['image_activity'])));
}

// Check if certificate is PDF
$currentCertIsPdf = false;
if (!empty($item['image_certificate'])) {
    $ext = strtolower(pathinfo($item['image_certificate'], PATHINFO_EXTENSION));
    $currentCertIsPdf = ($ext === 'pdf');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? '';
    $education_name = $_POST['education_name'] ?? '';
    $certificate_name = $_POST['certificate_name'] ?? '';
    $certificate_link = $_POST['certificate_link'] ?? '';
    
    // Keep existing images that weren't removed
    $kept_images = $_POST['kept_images'] ?? [];
    $activity_images = $kept_images;
    $image_certificate = $item['image_certificate'];
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    // Handle new activity images
    if (!empty($_FILES['activity_images']['name'][0])) {
        $file_count = count($_FILES['activity_images']['name']);
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['activity_images']['error'][$i] === UPLOAD_ERR_OK) {
                $filename = time() . '_activity_' . $i . '_' . basename($_FILES['activity_images']['name'][$i]);
                if (move_uploaded_file($_FILES['activity_images']['tmp_name'][$i], $target_dir . $filename)) {
                    $activity_images[] = $filename;
                }
            }
        }
    }
    
    // Handle certificate file
    if (!empty($_FILES['image_certificate']['name'])) {
        $filename = time() . '_cert_' . basename($_FILES['image_certificate']['name']);
        if (move_uploaded_file($_FILES['image_certificate']['tmp_name'], $target_dir . $filename)) {
            $image_certificate = $filename;
        }
    }
    
    $activity_images_string = implode(',', $activity_images);
    
    $stmt = $pdo->prepare("UPDATE education_and_certification_page SET category = ?, name_education_history = ?, image_activity = ?, name_certificate = ?, image_certificate = ?, link_certificate = ? WHERE id = ?");
    $stmt->execute([$category, $education_name, $activity_images_string, $certificate_name, $image_certificate, $certificate_link, $id]);
    header('Location: index.php?updated=1');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

<style>
.image-input-group {
    background: #f8f9fa;
    border: 2px dashed #ddd;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.image-input-group:hover { border-color: var(--accent); background: #fff5f6; }
.image-input-group .img-number {
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
}
.btn-remove-img {
    background: #e74c3c;
    color: white;
    border: none;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    cursor: pointer;
}
.btn-add-img {
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
    width: fit-content;
}
.btn-add-img .plus-icon {
    width: 24px;
    height: 24px;
    background: var(--gradient-accent);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.existing-img {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #e8f4e8;
    border: 2px solid #27ae60;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    margin-bottom: 0.75rem;
}
.existing-img img { width: 60px; height: 45px; object-fit: cover; border-radius: 6px; }
.existing-img .info { flex: 1; font-size: 0.85rem; color: #27ae60; }
.images-section {
    background: #f0f4f8;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
.images-section h5 { margin: 0 0 1rem 0; color: var(--primary); font-size: 0.95rem; }
.current-file {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}
.current-file.pdf-file { background: #fdf2f2; border: 1px solid #f5c6cb; }
.current-file img { max-width: 100px; border-radius: 8px; }
.pdf-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.file-type-badge { display: inline-flex; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
.file-type-badge.pdf { background: #e74c3c; color: white; }
.file-type-badge.image { background: #3498db; color: white; }
</style>

<div class="form-card">
    <div class="form-card-header">
        <h3>✏️ Edit Education/Certification</h3>
        <p>Update education or certification details</p>
    </div>
    
    <div class="form-card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" class="form-control">
                    <option value="">-- Select --</option>
                    <option value="Formal Education" <?php echo ($item['category'] ?? '') === 'Formal Education' ? 'selected' : ''; ?>>Formal Education</option>
                    <option value="Certificate" <?php echo ($item['category'] ?? '') === 'Certificate' ? 'selected' : ''; ?>>Certificate</option>
                    <option value="Online Course" <?php echo ($item['category'] ?? '') === 'Online Course' ? 'selected' : ''; ?>>Online Course</option>
                    <option value="Workshop" <?php echo ($item['category'] ?? '') === 'Workshop' ? 'selected' : ''; ?>>Workshop</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="education_name">Education/Institution Name</label>
                <input type="text" id="education_name" name="education_name" class="form-control" value="<?php echo htmlspecialchars($item['name_education_history'] ?? ''); ?>">
            </div>
            
            <!-- Multiple Activity Images (Carousel) -->
            <div class="images-section">
                <h5>🖼️ Activity Images (Carousel)</h5>
                
                <?php if (!empty($existing_images)): ?>
                    <p class="form-help" style="margin-bottom: 0.75rem;">Current images:</p>
                    <div id="existingImagesContainer">
                        <?php foreach ($existing_images as $idx => $img): ?>
                            <div class="existing-img" id="existing-img-<?php echo $idx; ?>">
                                <img src="/portofolio/assets/images/<?php echo htmlspecialchars($img); ?>" alt="Activity">
                                <span class="info">✓ Image #<?php echo $idx + 1; ?></span>
                                <input type="hidden" name="kept_images[]" value="<?php echo htmlspecialchars($img); ?>">
                                <button type="button" class="btn-remove-img" onclick="removeExisting(<?php echo $idx; ?>)">×</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <p class="form-help" style="margin: 1rem 0 0.75rem;">Add new images:</p>
                
                <div id="activityImagesContainer">
                    <div class="image-input-group" data-index="0">
                        <span class="img-number">+</span>
                        <input type="file" name="activity_images[]" class="form-control" accept="image/*">
                        <button type="button" class="btn-remove-img" onclick="removeImg(this)" style="display: none;">×</button>
                    </div>
                </div>
                
                <button type="button" class="btn-add-img" onclick="addMoreImages()">
                    <span class="plus-icon">+</span>
                    Add Another Image
                </button>
            </div>
            
            <div class="form-group">
                <label for="certificate_name">Certificate Name</label>
                <input type="text" id="certificate_name" name="certificate_name" class="form-control" value="<?php echo htmlspecialchars($item['name_certificate'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="image_certificate">Certificate File</label>
                <?php if ($item['image_certificate']): ?>
                    <div class="current-file <?php echo $currentCertIsPdf ? 'pdf-file' : ''; ?>">
                        <?php if ($currentCertIsPdf): ?>
                            <div class="pdf-icon">PDF</div>
                            <div><strong>Current PDF</strong><br><a href="/portofolio/assets/images/<?php echo htmlspecialchars($item['image_certificate']); ?>" target="_blank">View ↗</a></div>
                        <?php else: ?>
                            <img src="/portofolio/assets/images/<?php echo htmlspecialchars($item['image_certificate']); ?>">
                            <span>Current certificate</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <input type="file" id="image_certificate" name="image_certificate" class="form-control" accept="image/*,.pdf,application/pdf">
                <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                    <span class="file-type-badge image">📷 JPG/PNG</span>
                    <span class="file-type-badge pdf">📄 PDF</span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="certificate_link">Certificate Verification Link</label>
                <input type="url" id="certificate_link" name="certificate_link" class="form-control" value="<?php echo htmlspecialchars($item['link_certificate'] ?? ''); ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit"><span>💾</span> Update</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
let imgIndex = 1;

function addMoreImages() {
    const container = document.getElementById('activityImagesContainer');
    const newGroup = document.createElement('div');
    newGroup.className = 'image-input-group';
    newGroup.innerHTML = `
        <span class="img-number">+</span>
        <input type="file" name="activity_images[]" class="form-control" accept="image/*">
        <button type="button" class="btn-remove-img" onclick="removeImg(this)">×</button>
    `;
    container.appendChild(newGroup);
    imgIndex++;
    updateRemoveButtons();
}

function removeImg(btn) {
    btn.closest('.image-input-group').remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const groups = document.querySelectorAll('.image-input-group');
    groups.forEach(g => {
        g.querySelector('.btn-remove-img').style.display = groups.length > 1 ? 'flex' : 'none';
    });
}

function removeExisting(idx) {
    const el = document.getElementById('existing-img-' + idx);
    if (el) { el.style.opacity = '0'; setTimeout(() => el.remove(), 200); }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
