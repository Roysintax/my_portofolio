<?php
/**
 * Export Work Experience to Excel (CSV format)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$items = $pdo->query("SELECT * FROM work_experience_page ORDER BY id DESC")->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="work_experience_export_' . date('Y-m-d_His') . '.csv"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['ID', 'Company', 'Status', 'Start Date', 'End Date', 'Still Working', 'Description']);

foreach ($items as $item) {
    fputcsv($output, [
        $item['id'],
        $item['name_company'] ?? '-',
        $item['work_status'] ?? '-',
        $item['date_work_start'] ?? '-',
        $item['date_work_end'] ?? '-',
        $item['still_working'] ? 'Yes' : 'No',
        $item['activity_description'] ?? '-'
    ]);
}
fclose($output);
exit;
