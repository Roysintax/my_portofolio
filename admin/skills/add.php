<?php
/**
 * Admin - Add Skill
 * With JavaScript Canvas resize
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Add Skill';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['skill_name'] ?? '';
    $category = $_POST['skill_category'] ?? '';
    $level = $_POST['skill_level'] ?? 80;
    $order = $_POST['display_order'] ?? 0;
    $icon_size = $_POST['icon_size'] ?? 100;
    $icon = '';
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    // Handle base64 resized image from JavaScript
    if (!empty($_POST['resized_image'])) {
        $data = $_POST['resized_image'];
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif
            
            if (in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $data = base64_decode($data);
                if ($data !== false) {
                    $filename = time() . '_skill_' . $icon_size . 'px.' . $type;
                    if (file_put_contents($target_dir . $filename, $data)) {
                        $icon = $filename;
                    }
                }
            }
        }
    }
    // Fallback: regular file upload if no resized image
    elseif (!empty($_FILES['skill_icon']['name'])) {
        $filename = time() . '_skill_' . basename($_FILES['skill_icon']['name']);
        if (move_uploaded_file($_FILES['skill_icon']['tmp_name'], $target_dir . $filename)) {
            $icon = $filename;
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO skills (skill_name, skill_category, skill_level, skill_icon, icon_size, display_order) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $category, $level, $icon, $icon_size, $order])) {
        header('Location: index.php?added=1');
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<style>
.level-slider {
    -webkit-appearance: none;
    width: 100%;
    height: 10px;
    border-radius: 5px;
    background: #e0e0e0;
    outline: none;
}
.level-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: var(--accent);
    cursor: pointer;
}
.level-value {
    display: inline-block;
    min-width: 50px;
    text-align: center;
    font-weight: 700;
    color: var(--accent);
    font-size: 1.2rem;
}
.size-options {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 0.75rem;
}
.size-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border: 2px solid #ddd;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
    background: #f8f9fa;
}
.size-option:hover {
    border-color: var(--accent);
    background: #fff5f6;
}
.size-option input[type="radio"] {
    width: 18px;
    height: 18px;
    accent-color: var(--accent);
}
.size-option.selected {
    border-color: var(--accent);
    background: linear-gradient(135deg, #fff5f6 0%, #ffe8eb 100%);
}
.size-option .size-label {
    font-weight: 600;
    color: #333;
}
.size-option .size-desc {
    font-size: 0.75rem;
    color: #666;
}
.image-preview-container {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-top: 1rem;
    padding: 1.5rem;
    background: #f0f4f8;
    border-radius: 12px;
}
.preview-original, .preview-resized {
    text-align: center;
}
.preview-original img, .preview-resized img {
    max-width: 150px;
    max-height: 150px;
    border: 2px solid #ddd;
    border-radius: 8px;
    background: #fff;
}
.preview-resized img {
    border-color: var(--accent);
}
.preview-label {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.85rem;
    color: #666;
}
.preview-arrow {
    font-size: 2rem;
    color: var(--accent);
}
.hidden { display: none; }
</style>

<div class="form-card">
    <div class="form-card-header">
        <h3>⚡ Add New Skill</h3>
        <p>Add a skill to showcase your abilities</p>
    </div>
    
    <div class="form-card-body">
        <form method="POST" enctype="multipart/form-data" id="skillForm">
            <input type="hidden" name="resized_image" id="resizedImageData">
            
            <div class="form-group">
                <label for="skill_name">Skill Name <span class="required">*</span></label>
                <input type="text" id="skill_name" name="skill_name" class="form-control" placeholder="e.g. JavaScript, Photoshop, Laravel" required>
                <p class="form-help">📝 Nama skill/teknologi yang kamu kuasai</p>
            </div>
            
            <div class="form-group">
                <label for="skill_category">Category</label>
                <input type="text" id="skill_category" name="skill_category" class="form-control" placeholder="e.g. Programming, Design, Framework">
                <p class="form-help">📁 Kategori untuk mengelompokkan skill</p>
            </div>
            
            <div class="form-group">
                <label>Skill Level: <span class="level-value" id="levelDisplay">80</span>%</label>
                <input type="range" id="skill_level" name="skill_level" class="level-slider" min="0" max="100" value="80" oninput="document.getElementById('levelDisplay').textContent = this.value">
                <p class="form-help">📊 Tingkat keahlian (0% = pemula, 100% = expert)</p>
            </div>
            
            <div class="form-group">
                <label for="skill_icon">Skill Icon (Logo)</label>
                <input type="file" id="skill_icon" name="skill_icon" class="form-control" accept="image/*" onchange="handleImageSelect(this)">
                <p class="form-help">🖼️ Upload logo skill (PNG dengan background transparan)</p>
                
                <!-- Size Options -->
                <label style="margin-top: 1rem; display: block;">📐 Ukuran tampilan di halaman utama:</label>
                <div class="size-options">
                    <label class="size-option" onclick="resizeImage(50)">
                        <input type="radio" name="icon_size" value="50">
                        <div>
                            <div class="size-label">50 × 50 px</div>
                            <div class="size-desc">Kecil</div>
                        </div>
                    </label>
                    <label class="size-option selected" onclick="resizeImage(100)">
                        <input type="radio" name="icon_size" value="100" checked>
                        <div>
                            <div class="size-label">100 × 100 px</div>
                            <div class="size-desc">Standar</div>
                        </div>
                    </label>
                    <label class="size-option" onclick="resizeImage(150)">
                        <input type="radio" name="icon_size" value="150">
                        <div>
                            <div class="size-label">150 × 150 px</div>
                            <div class="size-desc">Sedang</div>
                        </div>
                    </label>
                    <label class="size-option" onclick="resizeImage(200)">
                        <input type="radio" name="icon_size" value="200">
                        <div>
                            <div class="size-label">200 × 200 px</div>
                            <div class="size-desc">Besar</div>
                        </div>
                    </label>
                </div>
                
                <!-- Image Preview -->
                <div class="image-preview-container hidden" id="previewContainer">
                    <div class="preview-original">
                        <img id="originalPreview" src="" alt="Original">
                        <span class="preview-label" id="originalSize">Original</span>
                    </div>
                    <div class="preview-arrow">→</div>
                    <div class="preview-resized">
                        <img id="resizedPreview" src="" alt="Resized">
                        <span class="preview-label" id="resizedSize">100 × 100 px</span>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="display_order">Display Order</label>
                <input type="number" id="display_order" name="display_order" class="form-control" value="0" min="0">
                <p class="form-help">🔢 Urutan tampil (angka lebih kecil = tampil duluan)</p>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit"><span>💾</span> Save Skill</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
let originalImage = null;
let currentSize = 100;

// Update size option selection styling
document.querySelectorAll('.size-option input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.size-option').forEach(opt => opt.classList.remove('selected'));
        this.closest('.size-option').classList.add('selected');
    });
});

function handleImageSelect(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            originalImage = new Image();
            originalImage.onload = function() {
                document.getElementById('originalPreview').src = e.target.result;
                document.getElementById('originalSize').textContent = `Original: ${originalImage.width} × ${originalImage.height} px`;
                document.getElementById('previewContainer').classList.remove('hidden');
                resizeImage(currentSize);
            };
            originalImage.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function resizeImage(size) {
    currentSize = size;
    document.getElementById('resizedSize').textContent = size + ' × ' + size + ' px';
    
    if (!originalImage) return;
    
    // Create canvas and resize
    const canvas = document.createElement('canvas');
    canvas.width = size;
    canvas.height = size;
    const ctx = canvas.getContext('2d');
    
    // Calculate crop for square aspect ratio
    const srcSize = Math.min(originalImage.width, originalImage.height);
    const srcX = (originalImage.width - srcSize) / 2;
    const srcY = (originalImage.height - srcSize) / 2;
    
    // Draw resized image
    ctx.drawImage(originalImage, srcX, srcY, srcSize, srcSize, 0, 0, size, size);
    
    // Get resized image as base64
    const resizedDataUrl = canvas.toDataURL('image/png');
    
    // Update preview
    document.getElementById('resizedPreview').src = resizedDataUrl;
    document.getElementById('resizedPreview').style.width = size + 'px';
    document.getElementById('resizedPreview').style.height = size + 'px';
    
    // Store in hidden field for form submission
    document.getElementById('resizedImageData').value = resizedDataUrl;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
