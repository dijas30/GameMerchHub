<?php
session_start();
require_once "includes/db.php";

// User info
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$isLoggedIn = isset($_SESSION['username']); // TRUE if user is logged in

// Cart info
$cart_total_items = 0;
$cart_total_price = 0;
if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $item){
        $cart_total_items += $item['quantity'];
        $cart_total_price += $item['price'] * $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMerch Hub – Terms & Digital Commerce Policy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" crossorigin="anonymous" />
    <link href="https://fonts.cdnfonts.com/css/frizen-land" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    <style>
        main.policy-container {
            max-width: 1000px;
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
            <a href="./home.php" class="nav-link"><img src="./image/Logo/Logo.png" alt="logolink"></a>
        </li>
        <li class="nav-list">
            <a href="./profile.php" class="nav-link"><span><?= htmlspecialchars($username) ?></span></a>
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
                    <span><i class="fa-solid fa-cart-shopping"></i> Cart <?= $cart_total_items ?> products – $<?= number_format($cart_total_price,2) ?></span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<main class="policy-container">
    <h2>Terms & Digital Commerce Policy</h2>

    <h3>1. Applicability & Legal Framework</h3>
    <ul>
        <li>These terms apply to all purchases made via GameMerch Hub, including digital products (game codes, gift cards) and physical goods.</li>
        <li>Online transactions conducted in Bangladesh are subject to several legal instruments including the Consumers’ Rights Protection Act, 2009, the Sale of Goods Act, 1930 and the Digital Commerce Operational Guidelines, 2021 issued by the Ministry of Commerce.</li>
        <li>GameMerch Hub reserves the right to update these terms at any time; such updates will be posted on this page and will apply to future transactions.</li>
    </ul>

    <h3>2. Digital Products & Delivery</h3>
    <ul>
        <li>Digital product keys, gift cards and codes will be delivered electronically via the communication method you select (email, account message) after payment clears.</li>
        <li>If a digital product fails to deliver due to our fault (expired code, invalid key etc), you are entitled to a replacement or refund in accordance with our <a href="./shipping_policy.php">Shipping Policy</a>.</li>
        <li>Delivery times may vary based on network conditions, server load and your local internet access environment.</li>
    </ul>

    <h3>3. Physical Goods</h3>
    <ul>
        <li>Physical goods will be shipped to the address you provide, subject to charges, transit delays and local customs/duties (if applicable).</li>
        <li>Ownership of the goods transfers to you when the carrier confirms delivery; until then risk is borne by us.</li>
    </ul>

    <h3>4. Returns, Refunds & Cancellations</h3>
    <ul>
        <li>For damaged physical items, non‑working digital keys or expired deals, you must report within 48 hours of receipt via WhatsApp: <strong>+1‑234‑567‑8901</strong> or via the <a href="./contact.php">Contact Us</a> page.</li>
        <li>Refunds will be processed if the issue is due to our error; items returned must be unused and in original condition (for physical goods).</li>
        <li>We may cancel orders if we suspect fraud, payment issues, or if delivery to your location is not possible. In such cases we will refund any cleared payment.</li>
    </ul>

    <h3>5. Customer Data, Privacy & Security</h3>
    <ul>
        <li>We collect your name, email, shipping address, payment information and game‑code history to fulfil orders. We will not share your personal data with third parties except: (a) to fulfil your order, (b) when required by law, or (c) with your consent.</li>
        <li>Under current Bangladeshi discussion, robust data protection frameworks are yet emerging. You agree that we may store and process your data in accordance with this policy.</li>
        <li>We use industry standard security (SSL/TLS) for transactions, but you accept that no online system is entirely risk‑free.</li>
    </ul>

    <h3>6. Dispute Resolution, Governing Law</h3>
    <ul>
        <li>These terms are governed by the laws of Bangladesh and any disputes will be subject to the jurisdiction of the courts of Bangladesh.</li>
        <li>If there is a dispute regarding an online consumer transaction, you may also approach the Directorate of National Consumer Rights Protection (DNCRP).</li>
    </ul>

    <h3>7. Miscellaneous</h3>
    <ul>
        <li>You must provide accurate contact and shipping information. We are not liable for delays caused by incorrect information.</li>
        <li>We reserve the right to refuse service to anyone who violates these terms or engages in fraudulent behaviour.</li>
        <li>If any part of these terms is found unenforceable, the remainder shall remain in full force.</li>
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
        <ul style="padding-top:10px;">
            <h3 style="color: rgb(255, 0, 255);">Follow Us</h3>
            <li><a href="#" style="color:#1877F2;font-size:2rem;"><i class="fa-brands fa-square-facebook"></i></a></li>
            <li><a href="#" style="color:white;font-size:2rem;"><i class="fa-brands fa-square-x-twitter"></i></a></li>
            <li><a href="#" style="font-size:2rem;"><i class="fa-brands fa-square-instagram" style="background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);-webkit-background-clip:text;-webkit-text-fill-color:transparent;"></i></a></li>
            <li><a href="#" style="color:#E60023;font-size:2rem;"><i class="fa-brands fa-square-pinterest"></i></a></li>
            <li><a href="#" style="color:#FF0000;font-size:2rem;"><i class="fa-brands fa-square-youtube"></i></a></li>
        </ul>
        <ul>
            <h3 style="color: rgb(255, 0, 255);">Help</h3>
            <li><a class="foot-link" href="./contact.php">Contact Us</a></li>
            <li><a class="foot-link" href="./Terms_and_conditions.php">Terms and Conditions</a></li>
        </ul>
    </div>
    <p style="padding-left:10px;padding-bottom:10px;">© 2025 GameMerchHub.com. All Rights Reserved.</p>
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
