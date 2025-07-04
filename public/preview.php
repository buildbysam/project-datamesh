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
            $file_type = $file['file_type'];

            if (file_exists($file_path)) {
                $file_extension = pathinfo($file['original_filename'], PATHINFO_EXTENSION);

                if (str_starts_with($file_type, 'image/')) {
                    header('Content-Type: ' . $file_type);
                    readfile($file_path);
                    exit;
                } elseif (str_starts_with($file_type, 'text/')) {
                    header('Content-Type: text/plain');
                    readfile($file_path);
                    exit;
                } else {
                    // For other types, just indicate no direct preview or offer download
                    header('Content-Type: text/html');
                    echo "<!DOCTYPE html><html><head><title>Preview</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-gray-100 flex items-center justify-center min-h-screen'><div class='text-center p-8 bg-white rounded-lg shadow-md'><p class='text-lg text-gray-700 mb-4'>No direct preview available for this file type.</p><a href='download.php?id=" . $file_id . "' class='bg-orange-500 text-white py-2 px-4 rounded-md hover:bg-orange-600'>Download File</a></div></body></html>";
                    exit;
                }
            } else {
                echo "<!DOCTYPE html><html><head><title>Error</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-gray-100 flex items-center justify-center min-h-screen'><div class='text-center p-8 bg-white rounded-lg shadow-md'><p class='text-lg text-red-700'>File not found.</p></div></body></html>";
                exit;
            }
        } else {
            echo "<!DOCTYPE html><html><head><title>Error</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-gray-100 flex items-center justify-center min-h-screen'><div class='text-center p-8 bg-white rounded-lg shadow-md'><p class='text-lg text-red-700'>Permission denied.</p></div></body></html>";
            exit;
        }
    } else {
        echo "<!DOCTYPE html><html><head><title>Error</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-gray-100 flex items-center justify-center min-h-screen'><div class='text-center p-8 bg-white rounded-lg shadow-md'><p class='text-lg text-red-700'>File not found in database.</p></div></body></html>";
        exit;
    }
} else {
    echo "<!DOCTYPE html><html><head><title>Error</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-gray-100 flex items-center justify-center min-h-screen'><div class='text-center p-8 bg-white rounded-lg shadow-md'><p class='text-lg text-red-700'>Invalid request.</p></div></body></html>";
    exit;
}
