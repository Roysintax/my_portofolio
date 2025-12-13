<?php
/**
 * Admin - Add Certification
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$pageTitle = 'Add Certification';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name_certificate'] ?? '';
    $issuer = $_POST['issuer'] ?? '';
    $issue_date = $_POST['issue_date'] ?? null;
    $link = $_POST['link_certificate'] ?? '';
    $image = '';
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portofolio/assets/images/';
    
    if (!empty($_FILES['image_certificate']['name'])) {
        $filename = time() . '_cert_' . basename($_FILES['image_certificate']['name']);
        if (move_uploaded_file($_FILES['image_certificate']['tmp_name'], $target_dir . $filename)) {
            $image = $filename;
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO certifications (name_certificate, issuer, issue_date, image_certificate, link_certificate) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $issuer, $issue_date ?: null, $image, $link])) {
        header('Location: index.php?added=1');
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<style>
.file-types { display: flex; gap: 0.5rem; margin-top: 0.5rem; }
.file-type-badge { padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
.file-type-badge.image { background: #e3f2fd; color: #1976d2; }
.file-type-badge.pdf { background: #ffebee; color: #c62828; }
</style>

<div class="form-card">
    <div class="form-card-header">
        <h3>🏆 Add Certification</h3>
        <p>Add a new certification or achievement</p>
    </div>
    
    <div class="form-card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name_certificate">Certificate Name <span class="required">*</span></label>
                <input type="text" id="name_certificate" name="name_certificate" class="form-control" placeholder="e.g. AWS Solutions Architect" required>
                <p class="form-help">📝 Nama sertifikat yang kamu dapatkan</p>
            </div>
            
            <div class="form-group">
                <label for="issuer">Issuer / Organization</label>
                <input type="text" id="issuer" name="issuer" class="form-control" placeholder="e.g. Amazon Web Services">
                <p class="form-help">🏢 Organisasi/lembaga yang menerbitkan sertifikat</p>
            </div>
            
            <div class="form-group">
                <label for="issue_date">Issue Date</label>
                <input type="date" id="issue_date" name="issue_date" class="form-control">
                <p class="form-help">📅 Tanggal sertifikat diterbitkan</p>
            </div>
            
            <div class="form-group">
                <label for="image_certificate">Certificate File</label>
                <input type="file" id="image_certificate" name="image_certificate" class="form-control" accept="image/*,application/pdf">
                <div class="file-types">
                    <span class="file-type-badge image">📷 JPG/PNG</span>
                    <span class="file-type-badge pdf">📄 PDF</span>
                </div>
                <p class="form-help">🖼️ <strong>Ukuran rekomendasi: 800x600 px</strong> atau file PDF sertifikat</p>
            </div>
            
            <div class="form-group">
                <label for="link_certificate">Certificate Link (Optional)</label>
                <input type="url" id="link_certificate" name="link_certificate" class="form-control" placeholder="https://credential.net/...">
                <p class="form-help">🔗 Link verifikasi sertifikat (jika tersedia)</p>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit"><span>💾</span> Save Certification</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
