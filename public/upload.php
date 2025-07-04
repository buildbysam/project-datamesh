<?php

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/core/auth.php';
require_once __DIR__ . '/../app/core/file_operations.php';
;

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_upload'])) {
    $user_id = $_SESSION['user_id'];
    $folder_id = isset($_POST['folder_id']) && $_POST['folder_id'] !== '' ? (int) $_POST['folder_id'] : null;
    $description = htmlspecialchars(trim($_POST['description']));
    $visibility = htmlspecialchars(trim($_POST['visibility']));
    $custom_filename = htmlspecialchars(trim($_POST['custom_filename']));

    $file = $_FILES['file_upload'];
    $original_filename = basename($file['name']);
    $file_size = $file['size'];
    $file_type = $file['type'];
    $tmp_name = $file['tmp_name'];
    $error = $file['error'];

    if ($error !== UPLOAD_ERR_OK) {
        header("Location: dashboard.php?error=upload_failed");
        exit;
    }

    $upload_dir = __DIR__ . '/../storage/uploads/' . $user_id . '/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);
    $stored_filename = uniqid('file_', true) . '.' . $file_extension;
    $file_path = $upload_dir . $stored_filename;

    if (move_uploaded_file($tmp_name, $file_path)) {
        $final_filename = empty($custom_filename) ? $original_filename : $custom_filename . '.' . $file_extension;
        if (uploadFile($user_id, $folder_id, $final_filename, $stored_filename, $file_path, $file_size, $file_type, $description, $visibility)) {
            header("Location: dashboard.php?success=file_uploaded&folder_id=" . ($folder_id ?? ''));
            exit;
        } else {
            unlink($file_path);
            header("Location: dashboard.php?error=db_insert_failed&folder_id=" . ($folder_id ?? ''));
            exit;
        }
    } else {
        header("Location: dashboard.php?error=move_failed&folder_id=" . ($folder_id ?? ''));
        exit;
    }
} else {
    header("Location: dashboard.php?error=no_file_selected");
    exit;
}
