<?php
require 'config/database.php';

try {
    // Dashboard
    $pdo->exec("ALTER TABLE dashboard MODIFY carrousel_image TEXT");
    $pdo->exec("ALTER TABLE dashboard MODIFY photo_profil TEXT");

    // Projects
    $pdo->exec("ALTER TABLE project_page MODIFY project_image TEXT");
    $pdo->exec("ALTER TABLE project_page MODIFY tool_logo TEXT");

    // Designs
    $pdo->exec("ALTER TABLE design_page MODIFY design_image TEXT");

    // Education
    $pdo->exec("ALTER TABLE education_and_certification_page MODIFY image_activity TEXT");
    $pdo->exec("ALTER TABLE education_and_certification_page MODIFY image_certificate TEXT");

    // Experience
    $pdo->exec("ALTER TABLE work_experience_page MODIFY image_activity_work_carrousel TEXT");

    echo "Database columns updated successfully to TEXT.";
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?>
