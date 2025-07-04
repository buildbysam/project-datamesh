<?php

require_once __DIR__ . '/../config/database.php';

function uploadFile($user_id, $folder_id, $original_filename, $stored_filename, $file_path, $file_size, $file_type, $description, $visibility)
{
    global $conn;
    $stmt = $conn->prepare("INSERT INTO files (user_id, folder_id, original_filename, stored_filename, file_path, file_size, file_type, description, visibility) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssisss", $user_id, $folder_id, $original_filename, $stored_filename, $file_path, $file_size, $file_type, $description, $visibility);
    return $stmt->execute();
}

function getFileById($file_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM files WHERE id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getFilesByUserId($user_id, $folder_id = null, $search_query = null)
{
    global $conn;
    $sql = "SELECT f.*, u.username FROM files f JOIN users u ON f.user_id = u.id WHERE f.user_id = ?";
    $params = [$user_id];
    $types = "i";

    if ($folder_id !== null) {
        $sql .= " AND f.folder_id = ?";
        $params[] = $folder_id;
        $types .= "i";
    } else {
        $sql .= " AND f.folder_id IS NULL";
    }

    if ($search_query) {
        $search_term = '%' . $search_query . '%';
        $sql .= " AND (f.original_filename LIKE ? OR f.description LIKE ? OR f.file_type LIKE ?)";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= "sss";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getPublicFiles($user_id, $search_query = null)
{
    global $conn;
    $sql = "SELECT f.*, u.username FROM files f JOIN users u ON f.user_id = u.id WHERE f.visibility = 'public' AND f.user_id != ?";
    $params = [$user_id];
    $types = "i";

    if ($search_query) {
        $search_term = '%' . $search_query . '%';
        $sql .= " AND (f.original_filename LIKE ? OR f.description LIKE ? OR f.file_type LIKE ?)";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= "sss";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function deleteFile($file_id, $user_id)
{
    global $conn;
    $file = getFileById($file_id);
    if ($file && $file['user_id'] == $user_id) {
        if (unlink($file['file_path'])) {
            $stmt = $conn->prepare("DELETE FROM files WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $file_id, $user_id);
            return $stmt->execute();
        }
    }
    return false;
}

function updateFileVisibility($file_id, $user_id, $visibility)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE files SET visibility = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $visibility, $file_id, $user_id);
    return $stmt->execute();
}

function moveFileToFolder($file_id, $user_id, $folder_id)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE files SET folder_id = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("iii", $folder_id, $file_id, $user_id);
    return $stmt->execute();
}
