<?php
/**
 * Export Skills to Excel (CSV)
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/portofolio/config/connect.php';

$items = $pdo->query("SELECT * FROM skills ORDER BY skill_category, display_order ASC")->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="skills_export_' . date('Y-m-d_His') . '.csv"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['ID', 'Skill Name', 'Category', 'Level (%)', 'Display Order']);

foreach ($items as $item) {
    fputcsv($output, [
        $item['id'],
        $item['skill_name'] ?? '-',
        $item['skill_category'] ?? '-',
        $item['skill_level'] ?? 0,
        $item['display_order'] ?? 0
    ]);
}
fclose($output);
exit;
