<?php
session_start();
require_once "includes/db.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize cart if empty
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $index = filter_input(INPUT_POST, 'index', FILTER_VALIDATE_INT);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    if ($index !== false && isset($_SESSION['cart'][$index])) {
        switch ($action) {
            case 'plus':
                $_SESSION['cart'][$index]['quantity']++;
                break;
            case 'minus':
                if ($_SESSION['cart'][$index]['quantity'] > 1) $_SESSION['cart'][$index]['quantity']--;
                break;
            case 'delete':
                array_splice($_SESSION['cart'], $index, 1);
                break;
        }
    }

    // Handle shipping selection
    if (isset($_POST['shipping_option'])) {
        $_SESSION['shipping_option'] = $_POST['shipping_option'];
    }

    header("Location: cart.php");
    exit();
}

// Fetch latest product info from DB safely
foreach ($_SESSION['cart'] as $key => $item) {
    $stmt = $pdo->prepare("SELECT name, price, stock_quantity, image_path, product_type FROM products WHERE name = ?");
    $stmt->execute([$item['name']]); // safe prepared statement
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        $_SESSION['cart'][$key]['price'] = (float)$product['price'];
        $_SESSION['cart'][$key]['image'] = $product['image_path'] ?? './image/placeholder.png';
        $_SESSION['cart'][$key]['product_type'] = $product['product_type'] ?? 'physical';
    } else {
        unset($_SESSION['cart'][$key]);
    }
}
$_SESSION['cart'] = array_values($_SESSION['cart']);

// Calculate subtotal and shipping
$subtotal = 0;
$shipping_cost = 0;
$selected_shipping = $_SESSION['shipping_option'] ?? "rajshahi";

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    if ($item['product_type'] === 'physical') {
        $shipping_cost += ($selected_shipping === 'rajshahi') ? 6 : 12;
    }
}

$total = $subtotal + $shipping_cost;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GameMerch Hub - Cart</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
<link href="https://fonts.cdnfonts.com/css/frizen-land" rel="stylesheet">
<link rel="stylesheet" href="./style.css">
<style>
/* Cart styling */
.cart { display:flex; gap:20px; flex-wrap:wrap; padding:20px; }
.cart-items, .cart-payment { flex:1; min-width:300px; }
.cart-item { display:flex; gap:10px; align-items:center; margin-bottom:15px; padding:10px; background:#111; border-radius:10px; }
.cart-item img { width:80px; height:80px; object-fit:cover; border-radius:6px; }
.cart-item-description { flex:1; color:white; }
.cart-items-action button { background:#ff00ff; color:#fff; border:none; border-radius:6px; padding:5px 10px; cursor:pointer; }
.cart-payment-summery { background:#111; padding:20px; border-radius:10px; color:white; }
.cart-payment-summery button { background:#ff00ff; color:white; border:none; padding:10px; border-radius:6px; cursor:pointer; width:100%; }
.shipping-option { margin-top:10px; color:#fff; }
.shipping-note { font-size:0.85rem; color:yellow; margin-top:5px; }
.disabled-link { pointer-events:none; color:grey !important; opacity:0.6; }
</style>
</head>
<body>

<nav>
<ul class="nav-upper flex-space-around">
    <li class="nav-list"><a href="./home.php" class="nav-link"><img src="./image/Logo/Logo.png" alt="logolink"></a></li>
    <li class="nav-list"><a href="./profile.php" class="nav-link"><span><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></span></a></li>
</ul>
<i class="fa-solid fa-bars fa-2x" id="menu-icon"></i>
<div id="menu" class="hidden">
    <ul class="nav-lower flex-space-around">
        <li class="nav-list"><a href="./register.php" class="nav-link <?php echo isset($_SESSION['user_id']) ? 'disabled-link' : ''; ?>">Register</a></li>
        <li class="nav-list"><a href="./contact.php" class="nav-link">Contact</a></li>
        <li class="nav-list"><a href="./login.php" class="nav-link <?php echo isset($_SESSION['user_id']) ? 'disabled-link' : ''; ?>">Login</a></li>
        <li class="nav-list"><a href="./logout.php" class="nav-link <?php echo !isset($_SESSION['user_id']) ? 'disabled-link' : ''; ?>">Logout</a></li>
        <li class="nav-list"><a href="./cart.php" class="nav-link"><i class="fa-solid fa-cart-shopping"></i> Cart</a></li>
    </ul>
</div>
</nav>

<main class="cart flex-center">
    <div class="cart-items">
        <div class="cart-items-heading card"><h2>Shopping Cart</h2></div>
        <?php if(empty($_SESSION['cart'])): ?>
            <p style="color:white; text-align:center;">Your cart is empty.</p>
        <?php endif; ?>
        <?php foreach ($_SESSION['cart'] as $index => $item): ?>
            <div class="cart-item">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="index" value="<?= (int)$index ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" title="Remove item"><i class="fa-solid fa-trash"></i></button>
                </form>

                <img src="<?= htmlspecialchars($item['image'] ?? './image/placeholder.png', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>">

                <div class="cart-item-description">
                    <h3><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <h4>Price: $<?= number_format((float)$item['price'],2) ?></h4>
                    <small>Type: <?= htmlspecialchars($item['product_type'], ENT_QUOTES, 'UTF-8') ?></small>
                </div>

                <div class="cart-items-action">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="index" value="<?= (int)$index ?>">
                        <input type="hidden" name="action" value="plus">
                        <button>+</button>
                    </form>
                    <span><?= (int)$item['quantity'] ?></span>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="index" value="<?= (int)$index ?>">
                        <input type="hidden" name="action" value="minus">
                        <button>-</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="cart-payment">
        <div class="cart-payment-summery card">
            <h2>Payment Summary</h2>
            <p>Subtotal: $<span id="subtotal"><?= number_format($subtotal,2) ?></span></p>

            <form method="POST">
                <div class="shipping-option">
                    <label>
                        <input type="radio" name="shipping_option" value="rajshahi" <?= $selected_shipping === 'rajshahi' ? 'checked' : '' ?>>
                        Within Rajshahi (6$ per physical product)
                    </label><br>
                    <label>
                        <input type="radio" name="shipping_option" value="outside" <?= $selected_shipping === 'outside' ? 'checked' : '' ?>>
                        Outside Rajshahi (12$ per physical product)
                    </label><br>
                    <button type="submit" style="margin-top:5px;">Update Shipping</button>
                </div>
            </form>

            <p>Shipping Cost: $<span id="shipping_cost"><?= number_format($shipping_cost,2) ?></span></p>
            <p>Total: $<span id="total"><?= number_format($total,2) ?></span></p>
            <p class="shipping-note">Delivery will be made to your profile address. Digital products have no shipping charge.</p>
            <a href="payment.php"><button>Pay Now</button></a>
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
<p style="padding: 10px">© 2015-2025 GameMerchHub.com. All Rights Reserved.</p>
</footer>

<script>
const menuIcon = document.getElementById('menu-icon');
const menu = document.getElementById('menu');
menuIcon.addEventListener('click', ()=> menu.classList.toggle('hidden'));
</script>

</body>
</html>
