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

    if ($file) {
        $is_owner = ($file['user_id'] == $user_id);
        $is_public = ($file['visibility'] == 'public');

        if ($is_owner || $is_public) {
            $file_path = $file['file_path'];
            if (file_exists($file_path)) {
                header('Content-Description: File Transfer');
                header('Content-Type: ' . $file['file_type']);
                header('Content-Disposition: attachment; filename="' . $file['original_filename'] . '"');
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . $file['file_size']);
                ob_clean();
                flush();
                readfile($file_path);
                exit;
            } else {
                header("Location: dashboard.php?error=file_not_found");
                exit;
            }
        } else {
            header("Location: dashboard.php?error=permission_denied");
            exit;
        }
    } else {
        header("Location: dashboard.php?error=file_not_found");
        exit;
    }
} else {
    header("Location: dashboard.php?error=invalid_request");
    exit;
}

?>