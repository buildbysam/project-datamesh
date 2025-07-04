<?php

require_once __DIR__ . '/../config/database.php';

function registerUser($username, $password)
{
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);
    return $stmt->execute();
}

function loginUser($username, $password)
{
    global $conn;
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            return true;
        }
    }
    return false;
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function logoutUser()
{
    session_unset();
    session_destroy();
}