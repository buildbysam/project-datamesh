<?php

require_once __DIR__ . '/../app/includes/header.php';
require_once __DIR__ . '/../app/core/auth.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
} else {
    header("Location: login.php");
    exit;
}
