<?php
// ─────────────────────────────────────────────────────────────────────────────
//  SSMS Uganda — Authentication & Session Helpers
// ─────────────────────────────────────────────────────────────────────────────

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Returns true if a user is logged in.
 */
function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

/**
 * Redirects to login page if the visitor is not authenticated.
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /modules/auth/login.php');
        exit;
    }
}

/**
 * Returns the current user's role name (e.g. 'Admin', 'Teacher').
 */
function currentRole(): string {
    return $_SESSION['role'] ?? '';
}

/**
 * Checks if the current user has one of the allowed roles.
 */
function hasRole(array $roles): bool {
    return in_array(currentRole(), $roles, true);
}

/**
 * Ends the session and redirects to login.
 */
function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    header('Location: /modules/auth/login.php');
    exit;
}

/**
 * Returns the current user's full name for display.
 */
function currentUserName(): string {
    return htmlspecialchars($_SESSION['full_name'] ?? 'User', ENT_QUOTES);
}

/**
 * Returns the current user's ID.
 */
function currentUserId(): int {
    return (int)($_SESSION['user_id'] ?? 0);
}
