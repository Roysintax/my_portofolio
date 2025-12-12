<?php
/**
 * Export Projects to Excel (CSV format)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$projects = $pdo->query("SELECT * FROM project_page ORDER BY id DESC")->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="projects_export_' . date('Y-m-d_His') . '.csv"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['ID', 'Title', 'Description', 'Total Logos', 'GitHub Link']);

foreach ($projects as $item) {
    $logos = !empty($item['tool_logo']) ? count(array_filter(explode(',', $item['tool_logo']))) : 0;
    fputcsv($output, [
        $item['id'],
        $item['title'] ?? 'Untitled',
        $item['description'] ?? '-',
        $logos . ' logo(s)',
        $item['project_link_github'] ?? '-'
    ]);
}
fclose($output);
exit;
