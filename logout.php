<?php
session_start();

// Store username before destroying session for display
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$isLoggedIn = isset($_SESSION['username']);

// Only destroy session if user was logged in
if ($isLoggedIn) {
    session_unset();
    session_destroy();
}

// Redirect to login page after 2 seconds
$redirect_url = "login.php";
header("refresh:2;url=$redirect_url");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GameMerch Hub - Logout</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://fonts.cdnfonts.com/css/frizen-land" rel="stylesheet">
<link rel="stylesheet" href="./style.css">
<style>
.disabled-link { pointer-events: none; color: grey !important; opacity: 0.5; }
.logout-message { 
    text-align: center; padding: 50px; color: #fff; background: #111; 
    border-radius: 10px; margin: 50px auto; max-width: 500px; 
}
.logout-message i { color: #ff00ff; margin-bottom: 15px; }
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
            <a href="./login.php" class="nav-link">
                <span><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></span>
            </a>
        </li>
    </ul>

    <i class="fa-solid fa-bars fa-2x" id="menu-icon"></i>

    <div id="menu" class="hidden">
        <ul class="nav-lower flex-space-around">
            <li class="nav-list">
                <a href="./register.php" class="nav-link <?= $isLoggedIn ? 'disabled-link' : ''; ?>">Register</a>
            </li>
            <li class="nav-list">
                <a href="./contact.php" class="nav-link">Contact</a>
            </li>
            <li class="nav-list">
                <a href="./login.php" class="nav-link <?= $isLoggedIn ? 'disabled-link' : ''; ?>">Login</a>
            </li>
            <li class="nav-list">
                <a href="./logout.php" class="nav-link <?= !$isLoggedIn ? 'disabled-link' : ''; ?>">Logout</a>
            </li>
            <li class="nav-list">
                <a href="./cart.php" class="nav-link">Cart</a>
            </li>
        </ul>
    </div>
</nav>

<main>
    <div class="logout-message">
        <i class="fa-solid fa-right-from-bracket" style="font-size:3rem;"></i>
        <h2>You have been logged out!</h2>
        <p>Redirecting to login page...</p>
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
                        style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i></a></li>
            <li><a href="#" style="color: #E60023; font-size: 2rem;"><i class="fa-brands fa-square-pinterest"></i></a></li>
            <li><a href="#" style="color: #FF0000; font-size: 2rem;"><i class="fa-brands fa-square-youtube"></i></a></li>
        </ul>
        <ul>
            <h3 style="color: rgb(255, 0, 255);">Help</h3>
            <li><a class="foot-link" href="./contact.php">Contact Us</a></li>
            <li><a class="foot-link" href="./Terms_and_conditions.php">Terms and Conditions</a></li>
        </ul>
    </div>
    <p style="padding-left: 10px; padding-bottom: 10px;">© 2025 GameMerchHub.com. All Rights Reserved.</p>
</footer>

<script>
const menuIcon = document.getElementById('menu-icon');
const menu = document.getElementById('menu');
menuIcon.addEventListener('click', () => menu.classList.toggle('hidden'));
</script>

</body>
</html>
