<?php

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/core/auth.php';
require_once __DIR__ . '/../app/core/file_operations.php';
;

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $file_id = (int) $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $file = getFileById($file_id);
    if ($file && $file['user_id'] == $user_id) {
        $folder_id_redirect = $file['folder_id'];
        if (deleteFile($file_id, $user_id)) {
            header("Location: dashboard.php?success=file_deleted&folder_id=" . ($folder_id_redirect ?? ''));
            exit;
        } else {
            header("Location: dashboard.php?error=delete_failed&folder_id=" . ($folder_id_redirect ?? ''));
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