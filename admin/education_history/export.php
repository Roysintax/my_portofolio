<?php
/**
 * Export Education History to Excel (CSV)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$items = $pdo->query("SELECT * FROM education_history ORDER BY id DESC")->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="education_history_export_' . date('Y-m-d_His') . '.csv"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['ID', 'Education Name', 'Description', 'Start Date', 'End Date', 'Still Studying']);

foreach ($items as $item) {
    fputcsv($output, [
        $item['id'],
        $item['name_education'] ?? '-',
        $item['description'] ?? '-',
        $item['start_date'] ?? '-',
        $item['end_date'] ?? '-',
        $item['still_studying'] ? 'Yes' : 'No'
    ]);
}
fclose($output);
exit;
