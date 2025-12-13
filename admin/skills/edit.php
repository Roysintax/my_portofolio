<?php
/**
 * Admin - Edit Skill
 * With image resize/upscale option
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Edit Skill';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM skills WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['skill_name'] ?? '';
    $category = $_POST['skill_category'] ?? '';
    $level = $_POST['skill_level'] ?? 80;
    $order = $_POST['display_order'] ?? 0;
    $target_size = $_POST['icon_size'] ?? 100;
    $icon = $item['skill_icon'];
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    if (!empty($_FILES['skill_icon']['name'])) {
        $tmp_file = $_FILES['skill_icon']['tmp_name'];
        $original_name = basename($_FILES['skill_icon']['name']);
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $filename = time() . '_skill_' . $target_size . 'px.' . $ext;
        
        // Check if GD library is available
        if (function_exists('imagecreatefrompng') && in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            list($orig_width, $orig_height) = getimagesize($tmp_file);
            
            $source = null;
            switch ($ext) {
                case 'jpg':
                case 'jpeg':
                    $source = @imagecreatefromjpeg($tmp_file);
                    break;
                case 'png':
                    $source = @imagecreatefrompng($tmp_file);
                    break;
                case 'gif':
                    $source = @imagecreatefromgif($tmp_file);
                    break;
                case 'webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $source = @imagecreatefromwebp($tmp_file);
                    }
                    break;
            }
            
            if ($source) {
                $new_image = imagecreatetruecolor($target_size, $target_size);
                
                if ($ext == 'png' || $ext == 'gif') {
                    imagealphablending($new_image, false);
                    imagesavealpha($new_image, true);
                    $transparent = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
                    imagefill($new_image, 0, 0, $transparent);
                }
                
                $size = min($orig_width, $orig_height);
                $src_x = ($orig_width - $size) / 2;
                $src_y = ($orig_height - $size) / 2;
                
                imagecopyresampled($new_image, $source, 0, 0, $src_x, $src_y, $target_size, $target_size, $size, $size);
                
                $output_path = $target_dir . $filename;
                switch ($ext) {
                    case 'jpg':
                    case 'jpeg':
                        imagejpeg($new_image, $output_path, 90);
                        break;
                    case 'png':
                        imagepng($new_image, $output_path, 9);
                        break;
                    case 'gif':
                        imagegif($new_image, $output_path);
                        break;
                    case 'webp':
                        if (function_exists('imagewebp')) {
                            imagewebp($new_image, $output_path, 90);
                        }
                        break;
                }
                
                imagedestroy($source);
                imagedestroy($new_image);
                $icon = $filename;
            } else {
                if (move_uploaded_file($tmp_file, $target_dir . $filename)) {
                    $icon = $filename;
                }
            }
        } else {
            // GD not available, just move the file
            if (move_uploaded_file($tmp_file, $target_dir . $filename)) {
                $icon = $filename;
            }
        }
    }
    
    $stmt = $pdo->prepare("UPDATE skills SET skill_name = ?, skill_category = ?, skill_level = ?, skill_icon = ?, display_order = ? WHERE id = ?");
    $stmt->execute([$name, $category, $level, $icon, $order, $id]);
    header('Location: index.php?updated=1');
    exit;
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
.current-icon {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f0f4f8;
    border-radius: 10px;
    margin-bottom: 0.75rem;
}
.current-icon img {
    width: 60px;
    height: 60px;
    object-fit: contain;
    background: #fff;
    border-radius: 8px;
    padding: 5px;
    border: 2px solid #ddd;
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
.size-preview {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 1rem;
    padding: 1rem;
    background: #f0f4f8;
    border-radius: 10px;
}
.size-preview .preview-box {
    border: 2px dashed var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--accent);
    font-weight: 600;
    transition: all 0.3s;
}
</style>

<div class="form-card">
    <div class="form-card-header">
        <h3>✏️ Edit Skill</h3>
    </div>
    
    <div class="form-card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="skill_name">Skill Name <span class="required">*</span></label>
                <input type="text" id="skill_name" name="skill_name" class="form-control" value="<?php echo htmlspecialchars($item['skill_name'] ?? ''); ?>" required>
                <p class="form-help">📝 Nama skill/teknologi yang kamu kuasai</p>
            </div>
            
            <div class="form-group">
                <label for="skill_category">Category</label>
                <input type="text" id="skill_category" name="skill_category" class="form-control" value="<?php echo htmlspecialchars($item['skill_category'] ?? ''); ?>">
                <p class="form-help">📁 Kategori untuk mengelompokkan skill</p>
            </div>
            
            <div class="form-group">
                <label>Skill Level: <span class="level-value" id="levelDisplay"><?php echo $item['skill_level'] ?? 80; ?></span>%</label>
                <input type="range" id="skill_level" name="skill_level" class="level-slider" min="0" max="100" value="<?php echo $item['skill_level'] ?? 80; ?>" oninput="document.getElementById('levelDisplay').textContent = this.value">
                <p class="form-help">📊 Tingkat keahlian (0% = pemula, 100% = expert)</p>
            </div>
            
            <div class="form-group">
                <label for="skill_icon">Skill Icon</label>
                <?php if (!empty($item['skill_icon'])): ?>
                    <div class="current-icon">
                        <img src="/portofolio/assets/images/<?php echo htmlspecialchars($item['skill_icon']); ?>" alt="Icon">
                        <div>
                            <strong>Icon saat ini</strong><br>
                            <span style="color: #666; font-size: 0.85rem;"><?php echo htmlspecialchars($item['skill_icon']); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                <input type="file" id="skill_icon" name="skill_icon" class="form-control" accept="image/*">
                <p class="form-help">🖼️ Upload logo baru (kosongkan jika tidak ingin mengubah)</p>
                
                <!-- Size Options -->
                <label style="margin-top: 1rem; display: block;">📐 Resize ke ukuran:</label>
                <div class="size-options">
                    <label class="size-option" onclick="updatePreview(50)">
                        <input type="radio" name="icon_size" value="50">
                        <div>
                            <div class="size-label">50 × 50 px</div>
                            <div class="size-desc">Kecil</div>
                        </div>
                    </label>
                    <label class="size-option selected" onclick="updatePreview(100)">
                        <input type="radio" name="icon_size" value="100" checked>
                        <div>
                            <div class="size-label">100 × 100 px</div>
                            <div class="size-desc">Standar (Rekomendasi)</div>
                        </div>
                    </label>
                    <label class="size-option" onclick="updatePreview(150)">
                        <input type="radio" name="icon_size" value="150">
                        <div>
                            <div class="size-label">150 × 150 px</div>
                            <div class="size-desc">Sedang</div>
                        </div>
                    </label>
                    <label class="size-option" onclick="updatePreview(200)">
                        <input type="radio" name="icon_size" value="200">
                        <div>
                            <div class="size-label">200 × 200 px</div>
                            <div class="size-desc">Besar</div>
                        </div>
                    </label>
                </div>
                
                <!-- Size Preview -->
                <div class="size-preview">
                    <div class="preview-box" id="sizePreviewBox" style="width: 100px; height: 100px;">
                        100px
                    </div>
                    <div>
                        <strong>Preview ukuran output</strong><br>
                        <span style="color: #666; font-size: 0.9rem;">Gambar baru akan di-resize ke ukuran ini</span>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="display_order">Display Order</label>
                <input type="number" id="display_order" name="display_order" class="form-control" value="<?php echo $item['display_order'] ?? 0; ?>" min="0">
                <p class="form-help">🔢 Urutan tampil (angka lebih kecil = tampil duluan)</p>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit"><span>💾</span> Update Skill</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.size-option input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.size-option').forEach(opt => opt.classList.remove('selected'));
        this.closest('.size-option').classList.add('selected');
    });
});

function updatePreview(size) {
    const box = document.getElementById('sizePreviewBox');
    box.style.width = size + 'px';
    box.style.height = size + 'px';
    box.textContent = size + 'px';
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
