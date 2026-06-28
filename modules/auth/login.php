<?php
// ─────────────────────────────────────────────────────────────────────────────
//  SSMS Uganda — Login Page
//  Connects to: users JOIN roles  (ssms_uganda)
// ─────────────────────────────────────────────────────────────────────────────
session_start();
require_once __DIR__ . '/../../config/database.php';

// Already logged in → go to dashboard
if (!empty($_SESSION['user_id'])) {
    header('Location: /modules/dashboard/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter your username and password.';
    } else {
        $stmt = db()->prepare(
            "SELECT u.id, u.password_hash, u.status, r.role_name,
                    COALESCE(s.first_name, '') AS first_name,
                    COALESCE(s.last_name,  '') AS last_name
             FROM   users u
             JOIN   roles r ON r.id = u.role_id
             LEFT   JOIN staff s ON s.user_id = u.id
             WHERE  u.username = ?
             LIMIT  1"
        );
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = 'Username not found.';
        } elseif ($user['status'] !== 'active') {
            $error = 'This account has been deactivated. Contact the administrator.';
        } elseif (!password_verify($password, $user['password_hash'])) {
            $error = 'Incorrect password.';
        } else {
            // ── Successful login ──────────────────────────────────────────
            session_regenerate_id(true);

            $full_name = trim($user['first_name'] . ' ' . $user['last_name']);
            if ($full_name === '') $full_name = ucfirst($username);

            $_SESSION['user_id']   = $user['id'];
            $_SESSION['role']      = $user['role_name'];
            $_SESSION['full_name'] = $full_name;
            $_SESSION['username']  = $username;

            // Record last login timestamp
            db()->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")
               ->execute([$user['id']]);

            header('Location: /modules/dashboard/index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — ssms_uganda</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>

<div class="login-page">

    <!-- Left brand panel -->
    <div class="login-brand">
        <div class="login-brand-icon">🏫</div>
        <h1>ssms_uganda</h1>
        <p>School Management System aligned to Uganda's New Lower Secondary Competency-Based Curriculum (CBC).</p>
        <div class="modules">
            <span class="login-module">Students</span>
            <span class="login-module">Staff</span>
            <span class="login-module">Results</span>
            <span class="login-module">Attendance</span>
            <span class="login-module">Finance</span>
            <span class="login-module">Library</span>
            <span class="login-module">Timetable</span>
            <span class="login-module">Report Cards</span>
        </div>
    </div>

    <!-- Right login panel -->
    <div class="login-panel">
        <h2>Welcome back</h2>
        <p>Sign in to your account to continue</p>

        <?php if ($error): ?>
        <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form" autocomplete="off">
            <div>
                <label class="login-label" for="username">Username</label>
                <input
                    class="form-control"
                    type="text"
                    id="username"
                    name="username"
                    placeholder="e.g. admin"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    required autofocus>
            </div>
            <div>
                <label class="login-label" for="password">Password</label>
                <input
                    class="form-control"
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    required>
            </div>
            <button type="submit" class="btn-login">Sign In →</button>
        </form>

        <div class="login-footer">
            Database: <span class="login-db">ssms_uganda</span><br>
            Default login: <strong>admin</strong> / <strong>ChangeMe123!</strong>
        </div>
    </div>

</div>

</body>
</html>
