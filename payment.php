<?php
session_start();
require_once "includes/db.php";

// Navbar login state
$isLoggedIn = isset($_SESSION['username']);

// Redirect if not logged in or cart empty
if (!$isLoggedIn || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Fetch user's address safely
$userStmt = $pdo->prepare("SELECT address FROM users WHERE id=?");
$userStmt->execute([$_SESSION['user_id']]);
$user = $userStmt->fetch();
$delivery_address = $user['address'] ?? 'N/A';

// Calculate subtotal & shipping
$subtotal = 0;
$shipping_cost = 0;
$selected_shipping = $_POST['shipping_option'] ?? 'rajshahi';

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    if ($item['product_type'] === 'physical') {
        $shipping_cost += ($selected_shipping === 'rajshahi') ? 6 : 12;
    }
}

// Initialize discount
$discount = 0;
$coupon_message = "";

// Handle coupon
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_coupon'])) {
    $coupon = trim($_POST['coupon']);
    $coupons = ["GAMER10"=>10, "SAVE20"=>20];

    if(isset($coupons[$coupon])) {
        $discount = $subtotal * ($coupons[$coupon] / 100);
        $coupon_message = "Coupon applied: {$coupons[$coupon]}% off!";
    } else {
        $coupon_message = "Invalid coupon code!";
    }
}

// Total calculation
$total = $subtotal - $discount + $shipping_cost;

// Handle actual payment submission
$payment_done = false;
$payment_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_now'])) {
    $payment_method = $_POST['payment_method'] ?? 'Credit Card';

    try {
        $pdo->beginTransaction();

        // Insert into orders safely
        $stmt = $pdo->prepare(
            "INSERT INTO orders 
            (user_id, total_amount, discount_amount, payment_method, payment_status, delivery_status, delivery_address)
            VALUES (?, ?, ?, ?, 'completed', 'pending', ?)"
        );
        $stmt->execute([
            $_SESSION['user_id'],
            $total,
            $discount,
            $payment_method,
            $delivery_address
        ]);

        $order_id = $pdo->lastInsertId();

        // Insert order items safely
        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_name, product_key, quantity, price)
                                   VALUES (?,?,?,?,?)");

        foreach ($_SESSION['cart'] as $item) {
            $itemStmt->execute([
                $order_id,
                $item['name'],
                null,
                $item['quantity'],
                $item['price']
            ]);
        }

        $pdo->commit();
        unset($_SESSION['cart']);
        $payment_done = true;

    } catch(Exception $e) {
        $pdo->rollBack();
        $payment_error = "Payment failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GameMerch Hub - Payment</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
<link href="https://fonts.cdnfonts.com/css/frizen-land" rel="stylesheet">
<link rel="stylesheet" href="./style.css">

<style>
.disabled-link { pointer-events: none; color: grey !important; opacity: 0.5; }
.card { background:#111; color:white; padding:20px; margin:10px; border-radius:10px; width:90%; max-width:500px; }
.payment-form input, .payment-form select, .payment-form button { width:100%; margin:10px 0; padding:10px; border-radius:5px; border:none; }
.payment-form button { background:#ff00ff; color:white; font-weight:bold; cursor:pointer; }
.alert-error { color:#ff5555; text-align:center; font-weight:bold; margin-bottom:10px; }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <ul class="nav-upper flex-space-around">
        <li class="nav-list">
            <a href="./home.php" class="nav-link">
                <img src="./image/Logo/Logo.png" alt="Logo">
            </a>
        </li>

        <li class="nav-list">
            <a class="nav-link">
                <span><?= $isLoggedIn ? htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') : "Guest" ?></span>
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
<!-- END NAV -->

<main style="display:flex; justify-content:center; flex-direction:column; align-items:center; padding:30px;">

<?php if(!$payment_done): ?>

    <?php if(!empty($payment_error)): ?>
        <div class="alert-error"><?= htmlspecialchars($payment_error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- Order Summary -->
    <div class="card">
        <h2>Order Summary</h2>
        <ul>
            <?php foreach($_SESSION['cart'] as $item): ?>
                <li><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?> x <?= (int)$item['quantity'] ?> — $<?= number_format($item['price'] * $item['quantity'],2) ?></li>
            <?php endforeach; ?>
        </ul>
        <p>Subtotal: $<?= number_format($subtotal,2) ?></p>
        <p>Shipping: $<?= number_format($shipping_cost,2) ?></p>
        <?php if($discount > 0): ?>
            <p>Discount: -$<?= number_format($discount,2) ?></p>
        <?php endif; ?>
        <h3>Total: $<?= number_format($total,2) ?></h3>
    </div>

    <!-- Coupon -->
    <form class="card" method="POST">
        <input type="text" name="coupon" placeholder="Enter coupon code">
        <button type="submit" name="apply_coupon">Apply Coupon</button>
        <?php if($coupon_message): ?>
            <p style="color:yellow; text-align:center;"><?= htmlspecialchars($coupon_message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </form>

    <!-- Payment Form -->
    <form class="card payment-form" method="POST">
        <h2>Payment Method</h2>
        <select name="payment_method" required>
            <option value="">Select Method</option>
            <option value="Credit Card">Credit Card</option>
            <option value="PayPal">PayPal</option>
            <option value="Gift Code">Gift Code</option>
        </select>
        <button type="submit" name="pay_now">Pay Now</button>
    </form>

<?php else: ?>

    <div class="card" style="text-align:center;">
        <h2>Payment Successful!</h2>
        <p>Your order has been placed successfully.</p>
        <a href="profile.php">
            <button style="background:#ff00ff;color:white;padding:10px;border:none;border-radius:6px;">
                Go to Profile
            </button>
        </a>
    </div>

<?php endif; ?>

</main>

</body>
</html>
