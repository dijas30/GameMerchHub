<?php
session_start();
require 'includes/db.php'; 

$errors = [];
$success = '';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

$isLoggedIn = isset($_SESSION['username']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Trim and sanitize inputs
    $username = htmlspecialchars(trim($_POST['username'] ?? ''), ENT_QUOTES, 'UTF-8');
    $fullname = htmlspecialchars(trim($_POST['fullname'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone    = htmlspecialchars(trim($_POST['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
    $address  = htmlspecialchars(trim($_POST['address'] ?? ''), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms    = isset($_POST['terms']);

    // Validation
    if (!$username || !$fullname || !$email || !$phone || !$address || !$password || !$confirm_password) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $errors[] = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (!$terms) {
        $errors[] = "You must agree to the Terms & Conditions.";
    }

    // Check duplicate username/email safely
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        if ($stmt->fetch()) {
            $errors[] = "Username or email already exists.";
        }
    }

    // Insert user if no errors
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (username, password_hash, full_name, email, phone, address)
            VALUES (:username, :password_hash, :full_name, :email, :phone, :address)
        ");
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':password_hash', $password_hash);
        $stmt->bindValue(':full_name', $fullname);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':phone', $phone);
        $stmt->bindValue(':address', $address);
        $stmt->execute();

        $success = "Registration successful! Redirecting to login page...";
        header("refresh:2;url=login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GameMerch Hub - Register</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
<link href="https://fonts.cdnfonts.com/css/frizen-land" rel="stylesheet">
<link rel="stylesheet" href="./style.css">
<style>
.disabled-link { pointer-events: none; color: grey !important; opacity: 0.6; }
</style>
</head>
<body>
<nav>
    <ul class="nav-upper flex-space-around">
        <li class="nav-list">
            <a href="./home.php" class="nav-link">
                <img src="./image/Logo/Logo.png" alt="logolink">
            </a>
        </li>
        <li class="nav-list">
            <a href="./profile.php" class="nav-link">
                <span><?= htmlspecialchars($_SESSION['username'] ?? 'Guest', ENT_QUOTES) ?></span>
            </a>
        </li>
    </ul>
    <i class="fa-solid fa-bars fa-2x" id="menu-icon"></i>
    <div id="menu" class="hidden">
        <ul class="nav-lower flex-space-around">
            <li class="nav-list"><a href="./register.php" class="nav-link <?= $isLoggedIn ? 'disabled-link' : '' ?>">Register</a></li>
            <li class="nav-list"><a href="./contact.php" class="nav-link">Contact</a></li>
            <li class="nav-list"><a href="./login.php" class="nav-link <?= $isLoggedIn ? 'disabled-link' : '' ?>">Login</a></li>
            <li class="nav-list"><a href="./logout.php" class="nav-link">Logout</a></li>
        </ul>
    </div>
</nav>

<main>
<div class="form-container">
    <div class="form-card card">
        <div class="form-header">
            <i class="fa-solid fa-user-plus"></i>
            <h2>Create New Account</h2>
            <p>Join us and start shopping!</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error-msg">
                <?php foreach($errors as $err) echo "<p style='color:red;'>".htmlspecialchars($err, ENT_QUOTES)."</p>"; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-msg">
                <p style="color:green;"><?= htmlspecialchars($success, ENT_QUOTES) ?></p>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="auth-form">
            <div class="form-group">
                <label for="username"><i class="fa-solid fa-user"></i> Username</label>
                <input type="text" id="username" name="username" placeholder="Choose a username" required value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES) ?>">
            </div>
            <div class="form-group">
                <label for="fullname"><i class="fa-solid fa-user"></i> Full Name</label>
                <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required value="<?= htmlspecialchars($_POST['fullname'] ?? '', ENT_QUOTES) ?>">
            </div>
            <div class="form-group">
                <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES) ?>">
            </div>
            <div class="form-group">
                <label for="phone"><i class="fa-solid fa-phone"></i> Phone</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES) ?>">
            </div>
            <div class="form-group">
                <label for="address"><i class="fa-solid fa-location-dot"></i> Address</label>
                <input type="text" id="address" name="address" placeholder="Enter your address" required value="<?= htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES) ?>">
            </div>
            <div class="form-group">
                <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password"><i class="fa-solid fa-lock"></i> Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            </div>
            <div class="form-options">
                <label class="checkbox-label">
                    <input type="checkbox" name="terms" required <?= isset($_POST['terms']) ? 'checked' : '' ?>>
                    <span>I agree to Terms & Conditions</span>
                </label>
            </div>
            <button type="submit" class="btn product-btn"><i class="fa-solid fa-user-plus"></i> Register</button>
            <div class="form-footer">
                <p>Already have an account? <a href="./login.php" class="link">Login here</a></p>
            </div>
        </form>
    </div>
</div>
</main>

<footer>
    <div class="footer-info flex-space-around">
        <ul>
            <h3 style="color: rgb(255, 0, 255);">GameMerchHub</h3>
            <li><a class="foot-link" href="home.php">Home</a></li>
            <li><a class="foot-link" href="about.php">About Us</a></li>
            <li><a class="foot-link" href="shipping_policy.php">Shipping Policy</a></li>
            <li><a class="foot-link" href="ESRB.php">ESRB Ratings</a></li>
        </ul>
    </div>
    <p style="padding-left: 10px; padding-bottom: 10px;">© 2015-2025 GameMerchHub.com. All Rights Reserved.</p>
</footer>
</body>
</html>
