<?php
session_start();
require_once "includes/db.php";

// Get the product ID from the URL safely
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id <= 0) {
    die("Invalid product ID.");
}

// Fetch product details safely
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();
if (!$product) {
    die("Product not found.");
}

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
$message = '';
$showAlert = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $currentCartQty = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['quantity'] : 0;

    if ($product['stock_quantity'] > 0) {
        if ($currentCartQty >= $product['stock_quantity']) {
            // Stock already full
            $message = "Stock limit reached! The order might get cancelled as stocks may get exhausted by this purchase!";
            $showAlert = true;
        } else {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += 1;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => 1,
                    'product_type' => $product['product_type']
                ];
            }

            if ($_SESSION['cart'][$product_id]['quantity'] == $product['stock_quantity']) {
                $message = "Stock limit reached! The order might get cancelled as stocks may get exhausted by this purchase!";
                $showAlert = true;
            } else {
                $message = "Added to cart!";
                $showAlert = false;
            }
        }
    } else {
        $message = "Out of stock!";
        $showAlert = false;
    }
}

// Total cart items
$cartItems = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartItems += $item['quantity'];
}

// Determine login state
$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GameMerch Hub - <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://fonts.cdnfonts.com/css/frizen-land" rel="stylesheet">
<link rel="stylesheet" href="./style.css">
<style>
.disabled-link { pointer-events: none; color: grey !important; opacity: 0.6; }
#cart-message {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #4caf50;
    color: white;
    padding: 12px 20px;
    border-radius: 5px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    z-index: 1000;
}
</style>
</head>
<body>

<?php if(!empty($message)) : ?>
<div id="cart-message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
<script>
    <?php if(!empty($showAlert)) : ?>
        alert("<?= addslashes($message) ?>");
    <?php else: ?>
        setTimeout(function(){
            var msg = document.getElementById('cart-message');
            if(msg) msg.style.display = 'none';
        }, 3000);
    <?php endif; ?>
</script>
<?php endif; ?>

<nav>
    <ul class="nav-upper flex-space-around">
        <li class="nav-list">
            <a href="./home.php" class="nav-link">
                <img src="./image/Logo/Logo.png" alt="logolink">
            </a>
        </li>
        <li class="nav-list">
            <a href="./profile.php" class="nav-link">
                <span><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></span>
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
                <a href="./cart.php" class="nav-link">
                    <span><i class="fa-solid fa-cart-shopping"></i> Cart <?= (int)$cartItems ?> items</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<main class="flex-center">
    <div class="main-content">
        <section class="product-details flex-center">
            <div class="product-details-left">
                <img class="product-details-img" src="<?= htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="product image">
            </div>
            <div class="product-details-right">
                <div class="product-info">
                    <div class="product-name" style="font-size: 2rem;"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></div>

                    <p class="product-description">
                        <strong>Description:</strong>
                        <?= !empty($product['description']) ? htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') : 'No description available'; ?>
                    </p>

                    <h4 class="product-category">
                        <strong>Category: <?= htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8') ?></strong>
                    </h4>

                    <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                    <div class="product-stock">Stock: <?= (int)$product['stock_quantity'] ?></div>
                    <div class="product-type">Type: <?= htmlspecialchars($product['product_type'], ENT_QUOTES, 'UTF-8') ?></div>

                    <form method="POST">
                        <button type="submit" name="add_to_cart" class="btn product-btn">Add To Cart</button>
                    </form>

                    <a href="./home.php"><button class="btn product-btn">Go back</button></a>
                </div>
            </div>
        </section>
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
                        style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
                        -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i></a></li>
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
</body>
</html>
