<?php
/**
 * Admin - Edit Certification
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Edit Certification';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM certifications WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) { header('Location: index.php'); exit; }

$isPdf = !empty($item['image_certificate']) && strtolower(pathinfo($item['image_certificate'], PATHINFO_EXTENSION)) === 'pdf';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name_certificate'] ?? '';
    $issuer = $_POST['issuer'] ?? '';
    $issue_date = $_POST['issue_date'] ?? null;
    $link = $_POST['link_certificate'] ?? '';
    $image = $item['image_certificate'];
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    if (!empty($_FILES['image_certificate']['name'])) {
        $filename = time() . '_cert_' . basename($_FILES['image_certificate']['name']);
        if (move_uploaded_file($_FILES['image_certificate']['tmp_name'], $target_dir . $filename)) {
            $image = $filename;
        }
    }
    
    $stmt = $pdo->prepare("UPDATE certifications SET name_certificate = ?, issuer = ?, issue_date = ?, image_certificate = ?, link_certificate = ? WHERE id = ?");
    $stmt->execute([$name, $issuer, $issue_date ?: null, $image, $link, $id]);
    header('Location: index.php?updated=1');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

<style>
.current-file { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f0f4f8; border-radius: 10px; margin-bottom: 1rem; }
.current-file img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
.pdf-preview { width: 80px; height: 80px; background: #e74c3c; color: white; display: flex; flex-direction: column; align-items: center; justify-content: center; border-radius: 8px; }
.file-types { display: flex; gap: 0.5rem; margin-top: 0.5rem; }
.file-type-badge { padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
.file-type-badge.image { background: #e3f2fd; color: #1976d2; }
.file-type-badge.pdf { background: #ffebee; color: #c62828; }
</style>

<div class="form-card">
    <div class="form-card-header">
        <h3>✏️ Edit Certification</h3>
    </div>
    
    <div class="form-card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name_certificate">Certificate Name <span class="required">*</span></label>
                <input type="text" id="name_certificate" name="name_certificate" class="form-control" value="<?php echo htmlspecialchars($item['name_certificate'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="issuer">Issuer / Organization</label>
                <input type="text" id="issuer" name="issuer" class="form-control" value="<?php echo htmlspecialchars($item['issuer'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="issue_date">Issue Date</label>
                <input type="date" id="issue_date" name="issue_date" class="form-control" value="<?php echo $item['issue_date'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="image_certificate">Certificate File</label>
                
                <?php if (!empty($item['image_certificate'])): ?>
                    <div class="current-file">
                        <?php if ($isPdf): ?>
                            <div class="pdf-preview">
                                <span style="font-size: 1.5rem;">📄</span>
                                <span style="font-size: 0.7rem;">PDF</span>
                            </div>
                        <?php else: ?>
                            <img src="/portofolio/assets/images/<?php echo htmlspecialchars($item['image_certificate']); ?>" alt="Certificate">
                        <?php endif; ?>
                        <div>
                            <strong>Current File</strong><br>
                            <span style="font-size: 0.85rem; color: #666;"><?php echo htmlspecialchars($item['image_certificate']); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <input type="file" id="image_certificate" name="image_certificate" class="form-control" accept="image/*,application/pdf">
                <div class="file-types">
                    <span class="file-type-badge image">📷 JPG/PNG</span>
                    <span class="file-type-badge pdf">📄 PDF</span>
                </div>
                <p class="form-help">Leave empty to keep current file</p>
            </div>
            
            <div class="form-group">
                <label for="link_certificate">Certificate Link</label>
                <input type="url" id="link_certificate" name="link_certificate" class="form-control" value="<?php echo htmlspecialchars($item['link_certificate'] ?? ''); ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit"><span>💾</span> Update Certification</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
