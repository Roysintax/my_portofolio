<?php
/**
 * Certifications Section Component
 * Uses new certifications table with PDF thumbnail preview using PDF.js
 */
?>

<!-- Certifications -->
<section class="section section-dark">
    <div class="container">
        <div class="section-header">
            <h2>Certifications</h2>
            <div class="line"></div>
        </div>
        
        <?php if (!empty($certifications)): ?>
            <div class="grid grid-3">
                <?php foreach ($certifications as $index => $cert): ?>
                    <?php 
                    // Check if certificate is PDF
                    $isPdf = false;
                    $certFile = $cert['image_certificate'] ?? '';
                    if (!empty($certFile)) {
                        $ext = strtolower(pathinfo($certFile, PATHINFO_EXTENSION));
                        $isPdf = ($ext === 'pdf');
                    }
                    ?>
                    <div class="education-card" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px);">
                        <!-- Issuer Badge -->
                        <?php if (!empty($cert['issuer'])): ?>
                            <span style="font-size: 0.85rem; font-weight: 600; color: var(--accent);">
                                <?php echo htmlspecialchars($cert['issuer']); ?>
                            </span>
                        <?php endif; ?>
                        
                        <h4 style="color: #fff;"><?php echo htmlspecialchars($cert['name_certificate'] ?? 'Certificate'); ?></h4>
                        
                        <!-- Issue Date -->
                        <?php if (!empty($cert['issue_date'])): ?>
                            <p style="font-size: 0.85rem; color: rgba(255,255,255,0.6); margin-top: 0.25rem;">
                                Issued: <?php echo date('M Y', strtotime($cert['issue_date'])); ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($certFile)): ?>
                            <?php if ($isPdf): ?>
                                <!-- PDF Certificate with Thumbnail Preview -->
                                <div class="certificate-pdf-preview">
                                    <a href="/portofolio/assets/images/<?php echo htmlspecialchars($certFile); ?>" target="_blank" class="pdf-thumbnail-link">
                                        <canvas id="pdf-canvas-<?php echo $index; ?>" class="pdf-thumbnail-canvas"></canvas>
                                        <div class="pdf-overlay">
                                            <span class="pdf-badge">PDF</span>
                                            <span class="pdf-view-text">Click to view</span>
                                        </div>
                                    </a>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        renderPdfThumbnail('/portofolio/assets/images/<?php echo htmlspecialchars($certFile); ?>', 'pdf-canvas-<?php echo $index; ?>');
                                    });
                                </script>
                            <?php else: ?>
                                <!-- Image Certificate -->
                                <div class="certificate-image">
                                    <img src="/portofolio/assets/images/<?php echo htmlspecialchars($certFile); ?>" alt="Certificate">
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php if (!empty($cert['link_certificate'])): ?>
                            <a href="<?php echo htmlspecialchars($cert['link_certificate']); ?>" target="_blank" class="certificate-link" style="color: #fff;">
                                Verify Certificate &#8599;
                            </a>
                        <?php elseif ($isPdf): ?>
                            <a href="/portofolio/assets/images/<?php echo htmlspecialchars($certFile); ?>" target="_blank" class="certificate-link" style="color: #fff;">
                                View Full PDF &#8599;
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state" style="padding: 2rem; color: rgba(255,255,255,0.6);">
                <p>No certifications added yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- PDF.js Library for rendering PDF thumbnails -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
// Set worker source
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

function renderPdfThumbnail(pdfUrl, canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    canvas.style.background = 'linear-gradient(135deg, #1a1a2e 0%, #16213e 100%)';
    
    pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
        pdf.getPage(1).then(function(page) {
            const desiredWidth = 300;
            const viewport = page.getViewport({ scale: 1 });
            const scale = desiredWidth / viewport.width;
            const scaledViewport = page.getViewport({ scale: scale });
            
            canvas.width = scaledViewport.width;
            canvas.height = scaledViewport.height;
            
            page.render({ canvasContext: ctx, viewport: scaledViewport });
        });
    }).catch(function(error) {
        console.log('PDF load error:', error);
        ctx.fillStyle = '#1a1a2e';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#fff';
        ctx.font = '14px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('PDF Preview', canvas.width/2, canvas.height/2);
    });
}
</script>

<style>
/* PDF Certificate Preview with Thumbnail */
.certificate-pdf-preview {
    margin-top: 1rem;
    border-radius: 12px;
    overflow: hidden;
}

.pdf-thumbnail-link {
    display: block;
    position: relative;
    text-decoration: none;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.pdf-thumbnail-link:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.pdf-thumbnail-canvas {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 12px;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    min-height: 200px;
}

.pdf-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1rem;
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.pdf-thumbnail-link:hover .pdf-overlay { opacity: 1; }

.pdf-badge {
    background: #e74c3c;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 1px;
}

.pdf-view-text {
    color: white;
    font-size: 0.9rem;
    font-weight: 500;
}

/* Certificate image styling */
.certificate-image {
    margin-top: 1rem;
    border-radius: 12px;
    overflow: hidden;
}

.certificate-image img {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.3s ease;
}

.certificate-image:hover img {
    transform: scale(1.02);
}
</style>
