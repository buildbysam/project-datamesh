<?php

require_once __DIR__ . '/../config/database.php';

function createFolder($user_id, $name, $parent_id = null)
{
    global $conn;
    if ($parent_id === null) {
        $stmt = $conn->prepare("INSERT INTO folders (user_id, name, parent_id) VALUES (?, ?, NULL)");
        $stmt->bind_param("is", $user_id, $name);
    } else {
        $stmt = $conn->prepare("INSERT INTO folders (user_id, name, parent_id) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $user_id, $name, $parent_id);
    }
    return $stmt->execute();
}

function getFoldersByUserId($user_id, $parent_id = null)
{
    global $conn;
    if ($parent_id === null) {
        $stmt = $conn->prepare("SELECT * FROM folders WHERE user_id = ? AND parent_id IS NULL");
        $stmt->bind_param("i", $user_id);
    } else {
        $stmt = $conn->prepare("SELECT * FROM folders WHERE user_id = ? AND parent_id = ?");
        $stmt->bind_param("ii", $user_id, $parent_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getFolderById($folder_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM folders WHERE id = ?");
    $stmt->bind_param("i", $folder_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function renameFolder($folder_id, $user_id, $new_name)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE folders SET name = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $new_name, $folder_id, $user_id);
    return $stmt->execute();
}

function deleteFolder($folder_id, $user_id, $action = 'delete_contents')
{
    global $conn;
    $folder = getFolderById($folder_id);
    if (!$folder || $folder['user_id'] != $user_id) {
        return false;
    }

    if ($action == 'move_to_root') {
        $stmt_files = $conn->prepare("UPDATE files SET folder_id = NULL WHERE folder_id = ? AND user_id = ?");
        $stmt_files->bind_param("ii", $folder_id, $user_id);
        $stmt_files->execute();

        $stmt_subfolders = $conn->prepare("UPDATE folders SET parent_id = NULL WHERE parent_id = ? AND user_id = ?");
        $stmt_subfolders->bind_param("ii", $folder_id, $user_id);
        $stmt_subfolders->execute();

    } elseif ($action == 'delete_contents') {
        $files_in_folder = getFilesByUserId($user_id, $folder_id);
        foreach ($files_in_folder as $file) {
            deleteFile($file['id'], $user_id);
        }
        $subfolders = getFoldersByUserId($user_id, $folder_id);
        foreach ($subfolders as $subfolder) {
            deleteFolder($subfolder['id'], $user_id, 'delete_contents');
        }
    }

    $stmt_folder = $conn->prepare("DELETE FROM folders WHERE id = ? AND user_id = ?");
    $stmt_folder->bind_param("ii", $folder_id, $user_id);
    return $stmt_folder->execute();
}
