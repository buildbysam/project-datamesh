<?php

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/core/auth.php';
require_once __DIR__ . '/../app/core/file_operations.php';
;
require_once __DIR__ . '/../app/core/folder_operations.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['file_id']) && isset($_POST['target_folder'])) {
    $file_id = (int) $_POST['file_id'];
    $target_folder_id = $_POST['target_folder'] === '' ? null : (int) $_POST['target_folder'];
    $user_id = $_SESSION['user_id'];

    $file = getFileById($file_id);

    if ($file && $file['user_id'] == $user_id) {
        if ($target_folder_id !== null) {
            $target_folder = getFolderById($target_folder_id);
            if (!$target_folder || $target_folder['user_id'] != $user_id) {
                header("Location: dashboard.php?error=invalid_target_folder");
                exit;
            }
        }

        if (moveFileToFolder($file_id, $user_id, $target_folder_id)) {
            header("Location: dashboard.php?success=file_moved&folder_id=" . ($file['folder_id'] ?? ''));
            exit;
        } else {
            header("Location: dashboard.php?error=file_move_failed&folder_id=" . ($file['folder_id'] ?? ''));
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
