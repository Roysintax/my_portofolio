<?php
/**
 * Export Designs to Excel (CSV format - compatible with Excel)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

// Fetch all designs
$designs = $pdo->query("SELECT * FROM design_page ORDER BY id DESC")->fetchAll();

// Set headers for CSV download (Excel compatible)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="designs_export_' . date('Y-m-d_His') . '.csv"');
header('Cache-Control: max-age=0');

// Open output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8 Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write header row
fputcsv($output, ['ID', 'Title', 'Total Images', 'Image Files', 'Design Link']);

// Write data rows
foreach ($designs as $design) {
    $images = [];
    if (!empty($design['design_image'])) {
        $images = array_filter(array_map('trim', explode(',', $design['design_image'])));
    }
    
    fputcsv($output, [
        $design['id'],
        $design['title'] ?? 'Untitled',
        count($images) . ' image(s)',
        implode('; ', $images),
        $design['design_link'] ?? '-'
    ]);
}

fclose($output);
exit;
