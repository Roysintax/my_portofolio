<?php
/**
 * Export Education to Excel (CSV format)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$items = $pdo->query("SELECT * FROM education_and_certification_page ORDER BY id DESC")->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="education_export_' . date('Y-m-d_His') . '.csv"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['ID', 'Category', 'Education Name', 'Certificate Name', 'Certificate Link']);

foreach ($items as $item) {
    fputcsv($output, [
        $item['id'],
        $item['category'] ?? '-',
        $item['name_education_history'] ?? '-',
        $item['name_certificate'] ?? '-',
        $item['link_certificate'] ?? '-'
    ]);
}
fclose($output);
exit;
