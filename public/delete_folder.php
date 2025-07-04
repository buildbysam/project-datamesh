<?php

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/core/auth.php';
require_once __DIR__ . '/../app/core/folder_operations.php';
require_once __DIR__ . '/../app/core/file_operations.php';
;

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['folder_id']) && isset($_POST['action'])) {
    $folder_id = (int) $_POST['folder_id'];
    $action = htmlspecialchars(trim($_POST['action'])); // 'delete_contents' or 'move_to_root'
    $user_id = $_SESSION['user_id'];

    $folder = getFolderById($folder_id);
    $parent_id_redirect = $folder['parent_id'];

    if ($folder && $folder['user_id'] == $user_id) {
        if (deleteFolder($folder_id, $user_id, $action)) {
            header("Location: dashboard.php?success=folder_deleted&folder_id=" . ($parent_id_redirect ?? ''));
            exit;
        } else {
            header("Location: dashboard.php?error=folder_deletion_failed&folder_id=" . ($parent_id_redirect ?? ''));
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