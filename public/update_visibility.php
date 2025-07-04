<?php

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/core/auth.php';
require_once __DIR__ . '/../app/core/file_operations.php';
;

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['file_id']) && isset($_POST['visibility'])) {
    $file_id = (int) $_POST['file_id'];
    $visibility = htmlspecialchars(trim($_POST['visibility']));
    $user_id = $_SESSION['user_id'];

    $file = getFileById($file_id);
    if ($file && $file['user_id'] == $user_id) {
        if ($visibility === 'private' || $visibility === 'public') {
            if (updateFileVisibility($file_id, $user_id, $visibility)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database update failed.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid visibility value.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Permission denied or file not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
