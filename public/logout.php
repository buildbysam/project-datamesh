<?php

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/core/auth.php';

logoutUser();

header("Location: login.php");
exit;
