<?php
// ─────────────────────────────────────────────────────────────────────────────
//  SSMS Uganda — Shared Page Header & Sidebar
//  Include this at the top of every authenticated page.
//  $page_title must be set before including this file.
// ─────────────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$role        = currentRole();
$user_name   = currentUserName();
$current_uri = $_SERVER['REQUEST_URI'];

$nav = [
    ['icon' => '🏠', 'label' => 'Dashboard',  'href' => '/modules/dashboard/index.php',
     'roles' => ['Admin','Head Teacher','Teacher','Bursar','Librarian']],
    ['icon' => '🎓', 'label' => 'Students',   'href' => '/modules/students/index.php',
     'roles' => ['Admin','Head Teacher','Teacher']],
    ['icon' => '👔', 'label' => 'Staff',       'href' => '/modules/staff/index.php',
     'roles' => ['Admin','Head Teacher']],
    ['icon' => '📋', 'label' => 'Attendance',  'href' => '/modules/attendance/index.php',
     'roles' => ['Admin','Head Teacher','Teacher']],
    ['icon' => '📊', 'label' => 'Results',     'href' => '/modules/reports/index.php',
     'roles' => ['Admin','Head Teacher','Teacher']],
    ['icon' => '💰', 'label' => 'Finance',     'href' => '/modules/finance/index.php',
     'roles' => ['Admin','Bursar']],
];

// Only show nav items the current role can access
$visible_nav = array_filter($nav, fn($item) => in_array($role, $item['roles'], true));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'SSMS') ?> — ssms_uganda</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>

<!-- ── Top Bar ─────────────────────────────────────────────────────── -->
<header class="topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle menu">☰</button>
        <span class="topbar-title">
            <span class="topbar-brand">SSMS</span>
            <span class="topbar-sep">›</span>
            <?= htmlspecialchars($page_title ?? '') ?>
        </span>
    </div>
    <div class="topbar-right">
        <span class="topbar-user">
            <span class="user-avatar"><?= strtoupper(substr($user_name, 0, 1)) ?></span>
            <span class="user-info">
                <span class="user-name"><?= $user_name ?></span>
                <span class="user-role"><?= htmlspecialchars($role) ?></span>
            </span>
        </span>
        <a href="/modules/auth/logout.php" class="btn-logout" title="Log out">⏻</a>
    </div>
</header>

<!-- ── Layout Wrapper ─────────────────────────────────────────────── -->
<div class="layout" id="layout">

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">🏫</div>
            <div class="logo-text">
                <span class="logo-name">ssms_uganda</span>
                <span class="logo-sub">School Management</span>
            </div>
        </div>
        <ul class="sidebar-nav">
            <?php foreach ($visible_nav as $item): ?>
            <li>
                <a href="<?= htmlspecialchars($item['href']) ?>"
                   class="sidebar-link <?= str_contains($current_uri, $item['href']) ? 'active' : '' ?>">
                    <span class="nav-icon"><?= $item['icon'] ?></span>
                    <span class="nav-label"><?= $item['label'] ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <div class="sidebar-footer">
            <span class="sidebar-db">📦 ssms_uganda</span>
        </div>
    </nav>

    <!-- Main content area (page content goes here) -->
    <main class="main-content">
