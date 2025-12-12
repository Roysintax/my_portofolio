<?php
/**
 * Admin Authentication Check
 * Include this at the top of all admin pages
 */

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: /portofolio/admin/login.php');
    exit;
}
