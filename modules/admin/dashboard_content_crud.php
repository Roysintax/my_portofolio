<?php
// Helper function for file upload
function uploadImage($file, $target_dir = "assets/images/uploads/") {
    if (isset($file['name']) && $file['error'] == 0) {
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($file["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                return $target_file;
            }
        }
    }
    return null;
}

// Handle Create/Update
if (isset($_POST['save_dashboard'])) {
    $carrousel_teks = $_POST['carrousel_teks'];
    $id = $_POST['id'];
    
    // Handle File Uploads
    $carrousel_image = $_POST['existing_carrousel_image'];
    if (!empty($_FILES['carrousel_image_file']['name'])) {
        $uploaded = uploadImage($_FILES['carrousel_image_file']);
        if ($uploaded) $carrousel_image = $uploaded;
    }

    $photo_profil = $_POST['existing_photo_profil'];
    
    // Handle cropped photo
    if (!empty($_POST['cropped_photo'])) {
        $imageData = $_POST['cropped_photo'];
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $decodedImage = base64_decode($imageData);
        
        $fileName = 'profile_' . time() . '.png';
        $filePath = 'assets/images/uploads/' . $fileName;
        
        if (file_put_contents($filePath, $decodedImage)) {
            $photo_profil = $filePath;
        }
    } elseif (!empty($_FILES['photo_profil_file']['name'])) {
        $uploaded = uploadImage($_FILES['photo_profil_file']);
        if ($uploaded) $photo_profil = $uploaded;
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE dashboard SET carrousel_teks=?, carrousel_image=?, photo_profil=? WHERE id=?");
        $stmt->execute([$carrousel_teks, $carrousel_image, $photo_profil, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO dashboard (carrousel_teks, carrousel_image, photo_profil) VALUES (?, ?, ?)");
        $stmt->execute([$carrousel_teks, $carrousel_image, $photo_profil]);
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM dashboard WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: ?page=admin&tab=dashboard_content");
    exit;
}

// Fetch Data
$stmt = $pdo->query("SELECT * FROM dashboard");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$edit_item = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM dashboard WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_item = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!-- Cropper.js CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">

<h2>Manage Dashboard Content</h2>
<p>Update your profile photo and carousel slides.</p>

<div class="glass-card" style="background: rgba(255,255,255,0.05); padding: 20px; margin-top: 20px;">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $edit_item['id'] ?? '' ?>">
        <div class="form-group">
            <label>Carousel Text</label>
            <input type="text" name="carrousel_teks" class="form-control" value="<?= $edit_item['carrousel_teks'] ?? '' ?>" required>
        </div>
        
        <div class="form-group">
            <label>Carousel Image</label>
            <input type="file" name="carrousel_image_file" class="form-control" accept="image/*">
            <input type="hidden" name="existing_carrousel_image" value="<?= $edit_item['carrousel_image'] ?? '' ?>">
            <?php if (!empty($edit_item['carrousel_image'])): ?>
                <div style="margin-top: 5px;">
                    <small>Current:</small><br>
                    <img src="<?= htmlspecialchars($edit_item['carrousel_image']) ?>" style="height: 50px; border-radius: 5px;">
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Profile Photo (dengan Crop)</label>
            <input type="file" name="photo_profil_file" id="profilePhotoInput" class="form-control" accept="image/*">
            <input type="hidden" name="existing_photo_profil" value="<?= $edit_item['photo_profil'] ?? '' ?>">
            <input type="hidden" name="cropped_photo" id="croppedPhotoData">
            
            <!-- Inline Crop Container -->
            <div id="cropContainer" style="display: none; margin-top: 20px; background: rgba(0,0,0,0.3); padding: 20px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1);">
                <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; align-items: flex-start;">
                    <!-- Crop Area -->
                    <div style="flex: 1; min-width: 300px; max-width: 500px;">
                        <h4 style="color: var(--accent-color); margin-bottom: 15px; text-align: center;">Crop Image</h4>
                        <div style="height: 400px; overflow: hidden; background: #000; border-radius: 10px; border: 1px solid rgba(255,255,255,0.2);">
                            <img id="cropImage" style="max-width: 100%; display: block;">
                        </div>
                    </div>
                    
                    <!-- Controls & Preview -->
                    <div style="flex: 1; min-width: 250px; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 20px;">
                        <div style="text-align: center;">
                            <h4 style="color: #fff; margin-bottom: 15px;">Preview</h4>
                            <div class="img-preview" style="width: 200px; height: 200px; overflow: hidden; border-radius: 50%; border: 3px solid var(--accent-color); box-shadow: 0 0 20px rgba(0, 210, 255, 0.3); margin: 0 auto;"></div>
                        </div>
                        
                        <div style="display: flex; gap: 15px; width: 100%; justify-content: center;">
                            <button type="button" id="applyCrop" style="
                                background: linear-gradient(135deg, #00d2ff, #0099cc);
                                color: #000;
                                border: none;
                                padding: 12px 30px;
                                border-radius: 25px;
                                font-weight: bold;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                flex: 1;
                                max-width: 150px;
                            ">✓ Terapkan</button>
                            
                            <button type="button" id="cancelCrop" style="
                                background: rgba(255, 255, 255, 0.1);
                                color: #fff;
                                border: 1px solid rgba(255, 255, 255, 0.2);
                                padding: 12px 30px;
                                border-radius: 25px;
                                font-weight: bold;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                flex: 1;
                                max-width: 150px;
                            ">✗ Batalkan</button>
                        </div>
                        <p style="font-size: 0.9rem; color: rgba(255,255,255,0.6); text-align: center;">
                            Scroll untuk zoom • Drag untuk geser
                        </p>
                    </div>
                </div>
            </div>

            <?php if (!empty($edit_item['photo_profil'])): ?>
                <div id="currentPhoto" style="margin-top: 15px;">
                    <small style="color: rgba(255,255,255,0.7);">Current Photo:</small><br>
                    <img src="<?= htmlspecialchars($edit_item['photo_profil']) ?>" style="height: 80px; width: 80px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.2); margin-top: 5px;">
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" name="save_dashboard" class="btn-primary" style="margin-top: 20px;"><?= $edit_item ? 'Update Content' : 'Add New Content' ?></button>
        <?php if ($edit_item): ?>
            <a href="?page=admin&tab=dashboard_content" class="btn-sm" style="background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); margin-left: 10px;">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-responsive" style="margin-top: 40px;">
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Text</th>
                <th>Profile Photo</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><img src="<?= htmlspecialchars($item['carrousel_image']) ?>" style="width: 80px; height: 45px; object-fit: cover; border-radius: 5px;"></td>
                <td><?= htmlspecialchars($item['carrousel_teks']) ?></td>
                <td><img src="<?= htmlspecialchars($item['photo_profil']) ?>" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover;"></td>
                <td>
                    <a href="?page=admin&tab=dashboard_content&edit=<?= $item['id'] ?>" class="btn-sm btn-edit">Edit</a>
                    <a href="?page=admin&tab=dashboard_content&delete=<?= $item['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Cropper.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<script>
    let cropper;
    const profilePhotoInput = document.getElementById('profilePhotoInput');
    const cropContainer = document.getElementById('cropContainer');
    const cropImage = document.getElementById('cropImage');
    const applyCropBtn = document.getElementById('applyCrop');
    const cancelCropBtn = document.getElementById('cancelCrop');
    const croppedPhotoData = document.getElementById('croppedPhotoData');
    const currentPhoto = document.getElementById('currentPhoto');

    if (profilePhotoInput) {
        profilePhotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    cropImage.src = event.target.result;
                    cropContainer.style.display = 'block';
                    if(currentPhoto) currentPhoto.style.display = 'none';
                    
                    // Initialize Cropper
                    if (cropper) {
                        cropper.destroy();
                    }
                    
                    cropper = new Cropper(cropImage, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 0.8,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                        preview: '.img-preview'
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        applyCropBtn.addEventListener('click', function() {
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({
                    width: 500,
                    height: 500,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });
                
                // Convert to base64 and store
                const croppedImageData = canvas.toDataURL('image/png');
                croppedPhotoData.value = croppedImageData;
                
                // Hide crop container but keep input value (or show success state)
                cropContainer.style.display = 'none';
                if(currentPhoto) currentPhoto.style.display = 'block';
                
                // Update current photo preview with cropped image temporarily
                if(currentPhoto) {
                    currentPhoto.querySelector('img').src = croppedImageData;
                }
                
                cropper.destroy();
                cropper = null;
            }
        });

        cancelCropBtn.addEventListener('click', function() {
            cropContainer.style.display = 'none';
            if(currentPhoto) currentPhoto.style.display = 'block';
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            profilePhotoInput.value = '';
            croppedPhotoData.value = '';
        });
    }
</script>
