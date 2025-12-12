<?php
/**
 * Admin - Add Education/Certification (with Multiple Images Carousel)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Add Education/Cert';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? '';
    $education_name = $_POST['education_name'] ?? '';
    $certificate_name = $_POST['certificate_name'] ?? '';
    $certificate_link = $_POST['certificate_link'] ?? '';
    $activity_images = [];
    $image_certificate = '';
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    // Handle multiple activity images (carousel)
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
    
    // Handle certificate file (image or PDF)
    if (!empty($_FILES['image_certificate']['name'])) {
        $filename = time() . '_cert_' . basename($_FILES['image_certificate']['name']);
        if (move_uploaded_file($_FILES['image_certificate']['tmp_name'], $target_dir . $filename)) {
            $image_certificate = $filename;
        }
    }
    
    // Store activity images as comma-separated string
    $activity_images_string = implode(',', $activity_images);
    
    $stmt = $pdo->prepare("INSERT INTO education_and_certification_page (category, name_education_history, image_activity, name_certificate, image_certificate, link_certificate) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$category, $education_name, $activity_images_string, $certificate_name, $image_certificate, $certificate_link]);
    header('Location: index.php?added=1');
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
    transition: all 0.3s ease;
}
.image-input-group:hover {
    border-color: var(--accent);
    background: #fff5f6;
}
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
    flex-shrink: 0;
}
.btn-remove-img {
    background: #e74c3c;
    color: white;
    border: none;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1rem;
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
    font-size: 0.9rem;
    width: fit-content;
}
.btn-add-img:hover {
    background: linear-gradient(135deg, #fff5f6 0%, #ffe8eb 100%);
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
    font-size: 1.2rem;
}
.images-section {
    background: #f0f4f8;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
.images-section h5 {
    margin: 0 0 1rem 0;
    color: var(--primary);
    font-size: 0.95rem;
}
.file-type-badge {
    display: inline-flex;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}
.file-type-badge.pdf { background: #e74c3c; color: white; }
.file-type-badge.image { background: #3498db; color: white; }
</style>

<div class="form-card">
    <div class="form-card-header">
        <h3>🎓 Add Education/Certification</h3>
        <p>Add education history or certification with carousel images</p>
    </div>
    
    <div class="form-card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" class="form-control">
                    <option value="">-- Select Category --</option>
                    <option value="Formal Education">Formal Education</option>
                    <option value="Certificate">Certificate</option>
                    <option value="Online Course">Online Course</option>
                    <option value="Workshop">Workshop</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="education_name">Education/Institution Name</label>
                <input type="text" id="education_name" name="education_name" class="form-control" placeholder="e.g. Universitas Indonesia">
            </div>
            
            <!-- Multiple Activity Images (Carousel) -->
            <div class="images-section">
                <h5>🖼️ Activity Images (Carousel)</h5>
                <p class="form-help" style="margin-bottom: 1rem;">Add multiple activity/graduation photos</p>
                
                <div id="activityImagesContainer">
                    <div class="image-input-group" data-index="0">
                        <span class="img-number">1</span>
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
                <input type="text" id="certificate_name" name="certificate_name" class="form-control" placeholder="e.g. AWS Certified Solutions Architect">
            </div>
            
            <div class="form-group">
                <label for="image_certificate">Certificate File (Image or PDF)</label>
                <input type="file" id="image_certificate" name="image_certificate" class="form-control" accept="image/*,.pdf,application/pdf">
                <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                    <span class="file-type-badge image">📷 JPG/PNG</span>
                    <span class="file-type-badge pdf">📄 PDF</span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="certificate_link">Certificate Verification Link</label>
                <input type="url" id="certificate_link" name="certificate_link" class="form-control" placeholder="https://verify.credential.com/...">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <span>💾</span> Save
                </button>
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
    newGroup.dataset.index = imgIndex;
    
    newGroup.innerHTML = `
        <span class="img-number">${imgIndex + 1}</span>
        <input type="file" name="activity_images[]" class="form-control" accept="image/*">
        <button type="button" class="btn-remove-img" onclick="removeImg(this)">×</button>
    `;
    
    container.appendChild(newGroup);
    imgIndex++;
    updateNumbers();
    updateRemoveButtons();
}

function removeImg(btn) {
    btn.closest('.image-input-group').remove();
    updateNumbers();
    updateRemoveButtons();
}

function updateNumbers() {
    document.querySelectorAll('.image-input-group').forEach((g, i) => {
        g.querySelector('.img-number').textContent = i + 1;
    });
}

function updateRemoveButtons() {
    const groups = document.querySelectorAll('.image-input-group');
    groups.forEach(g => {
        g.querySelector('.btn-remove-img').style.display = groups.length > 1 ? 'flex' : 'none';
    });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
