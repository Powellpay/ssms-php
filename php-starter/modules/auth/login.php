<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $pdo = get_db();

        $stmt = $pdo->prepare(
            'SELECT u.id, u.username, u.password_hash, r.role_name
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.username = :username AND u.status = "active"'
        );
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role_name'] = $user['role_name'];

            header('Location: /index.php');
            exit;
        }

        $error = 'Invalid username or password.';
    }
}

$page_title = 'Login';
require __DIR__ . '/../../includes/header.php';
?>

<div class="card" style="max-width:380px;margin:3rem auto;">
    <h2>Sign in</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn">Login</button>
    </form>

    <p style="margin-top:1rem;color:var(--muted);font-size:0.85rem;">
        Default seed account: <strong>admin</strong> / <strong>ChangeMe123!</strong>
        &mdash; change this immediately after first login.
    </p>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
