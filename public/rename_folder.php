<?php

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/core/auth.php';
require_once __DIR__ . '/../app/core/folder_operations.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['folder_id']) && isset($_POST['new_folder_name'])) {
    $folder_id = (int) $_POST['folder_id'];
    $new_name = htmlspecialchars(trim($_POST['new_folder_name']));
    $user_id = $_SESSION['user_id'];

    if (empty($new_name)) {
        header("Location: dashboard.php?error=new_folder_name_required&folder_id=" . ($folder_id ?? ''));
        exit;
    }

    $folder = getFolderById($folder_id);
    $parent_id_redirect = $folder['parent_id'];

    if ($folder && $folder['user_id'] == $user_id) {
        if (renameFolder($folder_id, $user_id, $new_name)) {
            header("Location: dashboard.php?success=folder_renamed&folder_id=" . ($parent_id_redirect ?? ''));
            exit;
        } else {
            header("Location: dashboard.php?error=folder_rename_failed&folder_id=" . ($parent_id_redirect ?? ''));
            exit;
        }
    } else {
        header("Location: dashboard.php?error=permission_denied");
        exit;
    }
} else {
    header("Location: dashboard.php?error=invalid_request");
    exit;
}
