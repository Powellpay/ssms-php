<?php
/**
 * Simple session-based auth guard.
 * Include this at the top of any page that requires a logged-in user.
 *
 * Usage:
 *   require_once __DIR__ . '/../includes/auth.php';
 *   require_login();                 // any logged-in user
 *   require_role(['Administrator']); // only specific roles
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: /modules/auth/login.php');
        exit;
    }
}

function require_role(array $allowed_roles): void
{
    require_login();

    if (!in_array($_SESSION['role_name'] ?? '', $allowed_roles, true)) {
        http_response_code(403);
        echo "You do not have permission to view this page.";
        exit;
    }
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    return [
        'id'        => $_SESSION['user_id'],
        'username'  => $_SESSION['username'] ?? '',
        'role_name' => $_SESSION['role_name'] ?? '',
    ];
}
