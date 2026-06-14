<?php
/**
 * Shared header. Expects $page_title to be set by the including page.
 */
$page_title = $page_title ?? 'School Management System';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> | SSMS</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="topbar">
    <div class="brand">SSMS - School Management System</div>
    <nav>
        <a href="/index.php">Dashboard</a>
        <a href="/modules/students/list.php">Students</a>
        <a href="/modules/assessment/">Assessment</a>
        <a href="/modules/reports/">Reports</a>
        <a href="/modules/finance/">Finance</a>
        <?php if ($user): ?>
            <span class="user-pill"><?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['role_name']) ?>)</span>
            <a href="/modules/auth/logout.php">Logout</a>
        <?php else: ?>
            <a href="/modules/auth/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
