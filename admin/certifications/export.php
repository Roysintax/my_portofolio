<?php
/**
 * Export Certifications to Excel (CSV)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$items = $pdo->query("SELECT * FROM certifications ORDER BY id DESC")->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="certifications_export_' . date('Y-m-d_His') . '.csv"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['ID', 'Certificate Name', 'Issuer', 'Issue Date', 'File', 'Link']);

foreach ($items as $item) {
    fputcsv($output, [
        $item['id'],
        $item['name_certificate'] ?? '-',
        $item['issuer'] ?? '-',
        $item['issue_date'] ?? '-',
        $item['image_certificate'] ?? '-',
        $item['link_certificate'] ?? '-'
    ]);
}
fclose($output);
exit;
