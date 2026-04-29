<?php
session_start();
require_once "includes/db.php";

/*  User Info  */
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$isLoggedIn = isset($_SESSION['username']);
$escapedUsername = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

/*  Cart Info  */
$cartItems = 0;
$cartTotal = 0.00;

if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $price = (float)$item['price'];
        $qty = (int)$item['quantity'];
        $cartTotal += $price * $qty;
        $cartItems += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMerch Hub – Shipping & Delivery Policy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link href="https://fonts.cdnfonts.com/css/frizen-land" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">

    <style>
        nav .nav-list a.nav-link {
            color: #fff;
            text-decoration: none;
        }

        .disabled-link {
            pointer-events: none;
            color: grey !important;
            opacity: 0.6;
        }

        main.policy-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: #1e1e2f;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
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
                <span><?php echo $escapedUsername; ?></span>
            </a>
        </li>
    </ul>

    <i class="fa-solid fa-bars fa-2x" id="menu-icon"></i>

    <div id="menu" class="hidden">
        <ul class="nav-lower flex-space-around">
            <li class="nav-list">
                <a href="./register.php" class="nav-link <?php echo $isLoggedIn ? 'disabled-link' : ''; ?>">Register</a>
            </li>

            <li class="nav-list">
                <a href="./contact.php" class="nav-link">Contact</a>
            </li>

            <li class="nav-list">
                <a href="./login.php" class="nav-link <?php echo $isLoggedIn ? 'disabled-link' : ''; ?>">Login</a>
            </li>

            <li class="nav-list">
                <a href="./logout.php" class="nav-link <?php echo !$isLoggedIn ? 'disabled-link' : ''; ?>">Logout</a>
            </li>

            <li class="nav-list">
                <a href="./cart.php" class="nav-link">
                    <span>
                        <i class="fa-solid fa-cart-shopping"></i>
                        Cart <?php echo $cartItems; ?> items – $<?php echo number_format($cartTotal,2); ?>
                    </span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<main class="policy-container">
    <h2>Shipping, Delivery & Return Policy</h2>

    <h3>1. Physical Goods Delivery</h3>
    <ul>
        <li>Delivery charges may vary depending on your location and selected courier service.</li>
        <li>Delivery time is an estimate and may change due to local conditions, holidays, or unforeseen delays.</li>
        <li>Ownership and risk of loss transfer to you upon confirmation of delivery by the carrier.</li>
    </ul>

    <h3>2. Digital Products (Game Keys, eProducts)</h3>
    <ul>
        <li>Digital products are delivered via email or your account dashboard immediately after successful payment.</li>
        <li>Delivery time may vary based on system load or server issues.</li>
        <li>If you do not receive your digital product, contact us through the <a href="./contact.php">Contact page</a>.</li>
    </ul>

    <h3>3. Complaints & Issues</h3>
    <ul>
        <li>For damaged items or non-working product keys, report within 48 hours of receipt.</li>
        <li>Reach us using the <a href="./contact.php">Contact Us</a> form.</li>
    </ul>

    <h3>4. Return & Refund Policy</h3>
    <ul>
        <li>Applicable for damaged items, non-working keys, or expired offers.</li>
        <li>Refunds or replacements are provided if reported within the allowed time.</li>
        <li>Physical items must be returned unused and in original packaging.</li>
    </ul>

    <h3>5. Additional Notes</h3>
    <ul>
        <li>We may refuse delivery if shipping information is incomplete or incorrect.</li>
        <li>Orders may be canceled if fraudulent activity is detected.</li>
        <li>Policies comply with global e-commerce standards.</li>
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
            <li><a href="#" style="font-size: 2rem;"><i class="fa-brands fa-square-instagram" style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i></a></li>
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

<script src="./home.js"></script>
<script src="./action.js"></script>
<script>
    const menuIcon = document.getElementById('menu-icon');
    const menu = document.getElementById('menu');
    menuIcon.addEventListener('click', () => menu.classList.toggle('hidden'));
</script>

</body>
</html>
