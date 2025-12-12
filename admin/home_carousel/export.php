<?php
/**
 * Export Home Carousel to Excel (CSV format)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$items = $pdo->query("SELECT * FROM home_carousel ORDER BY id DESC")->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="home_carousel_export_' . date('Y-m-d_His') . '.csv"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['ID', 'Title', 'Subtitle', 'Image Path']);

foreach ($items as $item) {
    fputcsv($output, [
        $item['id'],
        $item['title'] ?? '-',
        $item['subtitle'] ?? '-',
        $item['image_path'] ?? '-'
    ]);
}
fclose($output);
exit;
