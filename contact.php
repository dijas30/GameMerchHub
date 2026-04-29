<?php
session_start();
require_once "includes/db.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check login status for disabling Register/Login links
$isLoggedIn = isset($_SESSION['username']);

$ticket_submitted = false;
$error_message = '';

// Handle ticket submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_submit'])) {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($subject) || empty($message)) {
        $error_message = "Subject and message are required.";
    } else {
        // SQL injection safe prepared statement
        $stmt = $pdo->prepare("INSERT INTO tickets (user_id, subject, message) VALUES (?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $subject, $message])) {
            $ticket_submitted = true;
        } else {
            $error_message = "Failed to submit ticket. Please try again.";
        }
    }
}

// Fetch user's email and name securely
$stmt = $pdo->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GameMerch Hub - Submit Ticket</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
<link href="https://fonts.cdnfonts.com/css/frizen-land" rel="stylesheet">
<link rel="stylesheet" href="./style.css">
<style>
.ticket-page {
    max-width: 700px;
    width: 90%;
    background-color: #1e1e2f;
    padding: 40px 30px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.5);
    color: #fff;
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin: 50px auto;
}
.ticket-page h2 { text-align: center; font-size: 2.2rem; color: #ff00ff; margin-bottom: 20px; }
.ticket-form label { display: block; margin-bottom: 5px; font-weight: bold; }
.ticket-form input,
.ticket-form textarea {
    width: 100%;
    padding: 12px 15px;
    border-radius: 8px;
    border: 1px solid #555;
    background-color: #2b2b3c;
    color: #fff;
    font-size: 1rem;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}
.ticket-form input:focus,
.ticket-form textarea:focus {
    outline: none;
    border-color: #ff00ff;
    box-shadow: 0 0 8px #ff00ff;
}
.ticket-form button {
    background-color: #ff00ff;
    color: #fff;
    border: none;
    padding: 12px 25px;
    font-size: 1.1rem;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.ticket-form button:hover { background-color: #e600e6; }
.alert-success { color: #00ff88; text-align:center; font-weight:bold; }
.alert-error { color: #ff5555; text-align:center; font-weight:bold; }
@media (max-width:768px) { .ticket-page { padding: 30px 20px; } }

/* Disabled link styling */
.disabled-link {
    pointer-events: none;
    color: grey !important;
    opacity: 0.6;
}
</style>
</head>

<body>
<nav>
    <ul class="nav-upper flex-space-around">
        <li class="nav-list">
            <a href="./home.php" class="nav-link"><img src="./image/Logo/Logo.png" alt="Logo"></a>
        </li>
        <li class="nav-list">
            <a href="./profile.php" class="nav-link">
                <span><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></span>
            </a>
        </li>
    </ul>

    <i class="fa-solid fa-bars fa-2x" id="menu-icon"></i>

    <div id="menu" class="hidden">
        <ul class="nav-lower flex-space-around">

            <!-- Register disabled if logged in -->
            <li class="nav-list">
                <a href="./register.php"
                   class="nav-link <?= $isLoggedIn ? 'disabled-link' : ''; ?>">
                    Register
                </a>
            </li>

            <li class="nav-list"><a href="./contact.php" class="nav-link">Submit Ticket</a></li>

            <!-- Login disabled if logged in -->
            <li class="nav-list">
                <a href="./login.php"
                   class="nav-link <?= $isLoggedIn ? 'disabled-link' : ''; ?>">
                    Login
                </a>
            </li>

            <!-- Logout disabled if not logged in -->
            <li class="nav-list">
                <a href="./logout.php" class="nav-link <?= !$isLoggedIn ? 'disabled-link' : ''; ?>">Logout</a>
            </li>

            <li class="nav-list">
                <a href="./cart.php" class="nav-link">
                    <span><i class="fa-solid fa-cart-shopping"></i> 
                    Cart <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?> items</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<main class="flex-center">
    <div class="main-content ticket-page">
        <h2>Submit a Support Ticket</h2>

        <?php if ($ticket_submitted): ?>
            <div class="alert-success">✅ Ticket submitted successfully! Our team will contact you shortly.</div>
        <?php elseif (!empty($error_message)): ?>
            <div class="alert-error">❌ <?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="POST" class="ticket-form">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name"
                   value="<?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?>" readonly>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" readonly>

            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" placeholder="Ticket Subject" required>

            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="6" placeholder="Describe your issue..." required></textarea>

            <button type="submit" name="ticket_submit">Submit Ticket</button>
        </form>
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
        <ul style="padding-top: 10px;">
            <h3 style="color: rgb(255, 0, 255);">Follow Us</h3>
            <li><a href="#" style="color: #1877F2; font-size: 2rem;"><i class="fa-brands fa-square-facebook"></i></a></li>
            <li><a href="#" style="color: white; font-size: 2rem;"><i class="fa-brands fa-square-x-twitter"></i></a></li>
            <li><a href="#" style="font-size: 2rem;"><i class="fa-brands fa-square-instagram"
                style="background: linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);-webkit-background-clip: text;-webkit-text-fill-color: transparent;"></i></a></li>
            <li><a href="#" style="color: #E60023; font-size: 2rem;"><i class="fa-brands fa-square-pinterest"></i></a></li>
            <li><a href="#" style="color: #FF0000; font-size: 2rem;"><i class="fa-brands fa-square-youtube"></i></a></li>
        </ul>
        <ul>
            <h3 style="color: rgb(255, 0, 255);">Help</h3>
            <li><a class="foot-link" href="./contact.php">Contact Us</a></li>
            <li><a class="foot-link" href="./Terms_and_conditions.php">Terms and Conditions</a></li>
        </ul>
    </div>
    <p style="padding-left: 10px; padding-bottom: 10px;">© 2015-2025 GameMerchHub.com. All Rights Reserved.</p>
</footer>

<script>
const menuIcon = document.getElementById('menu-icon');
const menu = document.getElementById('menu');
menuIcon.addEventListener('click', () => menu.classList.toggle('hidden'));
</script>
</body>
</html>
