<?php

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/core/auth.php';
require_once __DIR__ . '/../app/core/folder_operations.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['folder_name'])) {
    $user_id = $_SESSION['user_id'];
    $folder_name = htmlspecialchars(trim($_POST['folder_name']));
    $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? (int) $_POST['parent_id'] : null;

    if (empty($folder_name)) {
        header("Location: dashboard.php?error=folder_name_required&folder_id=" . ($parent_id ?? ''));
        exit;
    }

    if (createFolder($user_id, $folder_name, $parent_id)) {
        header("Location: dashboard.php?success=folder_created&folder_id=" . ($parent_id ?? ''));
        exit;
    } else {
        header("Location: dashboard.php?error=folder_creation_failed&folder_id=" . ($parent_id ?? ''));
        exit;
    }
} else {
    header("Location: dashboard.php?error=invalid_request");
    exit;
}
