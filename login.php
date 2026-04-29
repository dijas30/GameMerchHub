<?php
session_start();
require 'includes/db.php';

$login_error = '';
$username_input = '';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? "admin.php" : "home.php"));
    exit();
}

// Handle Remember Me cookie
if (!empty($_COOKIE['remember_user'])) {
    $username_input = $_COOKIE['remember_user'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_input = trim($_POST['username'] ?? '');
    $password_input = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($username_input) || empty($password_input)) {
        $login_error = "Username and password are required.";
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password_hash, role, status FROM users WHERE username = ?");
        $stmt->execute([$username_input]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password_input, $user['password_hash'])) {
            $login_error = "Invalid username or password.";
        } elseif ($user['status'] !== 'active') {
            $login_error = "Your account is inactive. Please contact support.";
        } else {
            // Login OK
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Remember Me cookie
            if ($remember) {
                setcookie('remember_user', $user['username'], time() + 86400 * 7, "/");
            } else {
                setcookie('remember_user', '', time() - 3600, "/");
            }

            header("Location: " . ($user['role'] === 'admin' ? "admin.php" : "home.php"));
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMerch Hub - Login</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link href="https://fonts.cdnfonts.com/css/frizen-land" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">

    <style>
        .password-wrapper { position: relative; }
        .password-wrapper input { width: 100%; padding-right: 35px; }
        .password-wrapper i {
            position: absolute;
            right: 10px;
            top: 65%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <div class="form-card">

            <div class="form-header">
                <i class="fa-solid fa-user"></i>
                <h2>Login</h2>
                <p>Enter your username and password to login</p>
            </div>

            <?php if ($login_error): ?>
                <p style="color:red; text-align:center;">
                    <?= htmlspecialchars($login_error) ?>
                </p>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="login.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input 
                        type="text"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars($username_input) ?>"
                        required
                    >
                </div>

                <div class="form-group password-wrapper">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <i class="fa-solid fa-eye" id="togglePassword"></i>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember"
                            <?= !empty($_COOKIE['remember_user']) ? 'checked' : '' ?>>
                        Remember me
                    </label>

                    <a href="#" class="forgot-link" id="forgotPassword">Forgot Password?</a>
                </div>

                <button type="submit">Login</button>
            </form>

            <div class="form-footer">
                <p>Don't have an account? 
                    <a href="register.php" class="link">Register Here</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Forgot Password Popup -->
    <div id="forgotModal" style="
        display:none; position:fixed; top:0; left:0;
        width:100%; height:100%; background:rgba(0,0,0,0.8);
        color:white; justify-content:center; align-items:center;
    ">
        <div style="
            background:#222; padding:20px; border-radius:10px;
            width:90%; max-width:400px; text-align:center;
        ">
            <h3>Forgot Password</h3>
            <p>An OTP will be sent to your registered phone/email.</p>
            <p>Or contact: <br>
                WhatsApp: +880165464562 <br>
                Email: 
                <a href="mailto:support.gamemerchhub2025@gmail.com" style="color:#ff00ff;">
                    support.gamemerchhub2025@gmail.com
                </a>
            </p>

            <button id="closeModal" style="padding:10px 20px; cursor:pointer;">Close</button>
        </div>
    </div>

    <script>
        // Password visibility toggle
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");

        togglePassword.addEventListener("click", () => {
            const showing = passwordInput.type === "text";
            passwordInput.type = showing ? "password" : "text";

            togglePassword.classList.toggle("fa-eye", showing);
            togglePassword.classList.toggle("fa-eye-slash", !showing);
        });

        // Forgot password modal
        const modal = document.getElementById("forgotModal");
        document.getElementById("forgotPassword").addEventListener("click", (e) => {
            e.preventDefault();
            modal.style.display = "flex";
        });
        document.getElementById("closeModal").addEventListener("click", () => {
            modal.style.display = "none";
        });
    </script>
</body>
</html>
