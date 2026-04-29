<?php
session_start();
require_once "includes/db.php";

// Determine username safely
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Cart placeholder
$cart_total_items = 0;
$cart_total_price = 0;
if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $item){
        $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;
        $price = isset($item['price']) ? (float)$item['price'] : 0.0;
        $cart_total_items += $quantity;
        $cart_total_price += $price * $quantity;
    }
}

// Login state
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMerch Hub - Shipping Policy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.cdnfonts.com/css/frizen-land" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    <style>
        main.policy-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 30px;
            background-color: #1e1e2f;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
            color: #fff;
            line-height: 1.6;
        }

        main.policy-container h2 {
            text-align: center;
            color: #ff00ff;
            margin-bottom: 30px;
            font-size: 2rem;
        }

        main.policy-container h3 {
            color: #ff00ff;
            margin-top: 20px;
        }

        main.policy-container ul {
            padding-left: 20px;
        }

        main.policy-container li {
            margin-bottom: 10px;
        }

        main.policy-container a {
            color: #00ffff;
            text-decoration: underline;
        }

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
            <a href="./home.php" class="nav-link">
                <img src="./image/Logo/Logo.png" alt="logolink">
            </a>
        </li>
        <li class="nav-list">
            <a href="./profile.php" class="nav-link">
                <span><?= htmlspecialchars($username, ENT_QUOTES) ?></span>
            </a>
        </li>
    </ul>
    <i class="fa-solid fa-bars fa-2x" id="menu-icon"></i>
    <div id="menu" class="hidden">
        <ul class="nav-lower flex-space-around">
            <li class="nav-list">
                <a href="./register.php" class="nav-link <?= $isLoggedIn ? 'disabled-link' : '' ?>">Register</a>
            </li>
            <li class="nav-list"><a href="./contact.php" class="nav-link">Contact</a></li>
            <li class="nav-list">
                <a href="./login.php" class="nav-link <?= $isLoggedIn ? 'disabled-link' : '' ?>">Login</a>
            </li>
            <li class="nav-list">
                <a href="./logout.php" class="nav-link <?= !$isLoggedIn ? 'disabled-link' : '' ?>">Logout</a>
            </li>
            <li class="nav-list">
                <a href="./cart.php" class="nav-link">
                    <span><i class="fa-solid fa-cart-shopping"></i> Cart <?= (int)$cart_total_items ?> products - $<?= number_format((float)$cart_total_price,2) ?></span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<main class="policy-container">
    <h2>Shipping Policy</h2>

    <h3>Home Delivery</h3>
    <ul>
        <li>Delivery charges may vary based on your location and the total weight of the order.</li>
        <li>Expected delivery time for home delivery is 3-7 business days depending on your area.</li>
        <li>For any delays or issues with home delivery, please contact our support team via WhatsApp: <strong>+1-234-567-8901</strong> or email us using the <a href="./contact.php">Contact Us</a> page.</li>
    </ul>

    <h3>Online Delivery (Digital Products)</h3>
    <ul>
        <li>Delivery of e-products (digital games, gift cards, etc.) is usually instant but may take longer during high traffic periods.</li>
        <li>Delivery times may vary depending on server load and order volume.</li>
        <li>If you experience any issues receiving your digital product, contact our support team via WhatsApp: <strong>+1-234-567-8901</strong> or email through the <a href="./contact.php">Contact Us</a> page.</li>
    </ul>

    <h3>Complaints & Issues</h3>
    <ul>
        <li>If your order is lost, damaged, or delayed, please report immediately via WhatsApp: <strong>+1-234-567-8901</strong> or the <a href="./contact.php">Contact Us</a> page.</li>
        <li>We strive to respond to all complaints within 24 hours during business days.</li>
        <li>Refunds and replacements are handled according to our Terms and Conditions: <a href="./Terms_and_conditions.php">View Terms</a>.</li>
    </ul>

    <h3>Return Policy</h3>
    <ul>
        <li>Applicable for damaged items, non-working product keys, expired deals, or incorrect digital products.</li>
        <li>Please contact us within 48 hours of receiving your order via WhatsApp: <strong>+1-234-567-8901</strong> or <a href="./contact.php">Contact Us</a> to initiate a return or replacement.</li>
        <li>Returns are processed in accordance with our Terms and Conditions: <a href="./Terms_and_conditions.php">View Terms</a>.</li>
    </ul>

    <h3>General</h3>
    <ul>
        <li>Please ensure your delivery address and contact details are accurate to avoid delays.</li>
        <li>GameMerchHub is not responsible for delivery delays caused by courier services or external factors beyond our control.</li>
        <li>All shipping policies are subject to change, but updates will always be posted on this page.</li>
    </ul>
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
                        style="background: linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);-webkit-background-clip:text;-webkit-text-fill-color:transparent;"></i></a></li>
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

<script src="./home.js"></script>
<script src="./action.js"></script>
<script>
    const menuIcon = document.getElementById('menu-icon');
    const menu = document.getElementById('menu');
    menuIcon.addEventListener('click', () => menu.classList.toggle('hidden'));
</script>
</body>

</html>
