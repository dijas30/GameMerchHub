<?php
session_start();
require_once "includes/db.php";

// ---------- User info ----------
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$isLoggedIn = isset($_SESSION['username']); // used for disabling links

// Escape for safe output
$escapedUsername = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

// ---------- Cart info ----------
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

// ---------- Fetch products from database ----------
try {
    // Using prepared statement for consistency & security
    $stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
    $products = [];
}

// ---------- Organize products by category ----------
$categories = [];

foreach ($products as $product) {
    $category = htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8');
    $catKey = strtolower(str_replace(' ', '', $category));
    $categories[$catKey][] = $product;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMerch Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link href="https://fonts.cdnfonts.com/css/frizen-land" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">

    <style>
        /* Navbar hover improvement */
        nav .nav-list a.nav-link {
            position: relative;
            transition: color 0.3s ease;
        }
        nav .nav-list a.nav-link::after {
            content: "";
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #ff00ff;
            transition: width 0.3s ease;
        }
        nav .nav-list a.nav-link:hover::after {
            width: 100%;
        }

        /* Disable + greyed out links */
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
                <img src="./image/Logo/Logo.png" alt="logo">
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

            <!-- Register (disabled if logged in) -->
            <li class="nav-list">
                <a href="./register.php" class="nav-link <?php echo $isLoggedIn ? 'disabled-link' : ''; ?>">
                    Register
                </a>
            </li>

            <li class="nav-list">
                <a href="./contact.php" class="nav-link">Contact</a>
            </li>

            <!-- Login (disabled if logged in) -->
            <li class="nav-list">
                <a href="./login.php" class="nav-link <?php echo $isLoggedIn ? 'disabled-link' : ''; ?>">
                    Login
                </a>
            </li>

            <!-- Logout (disabled if NOT logged in) -->
            <li class="nav-list">
                <a href="./logout.php" class="nav-link <?php echo !$isLoggedIn ? 'disabled-link' : ''; ?>">
                    Logout
                </a>
            </li>

            <li class="nav-list">
                <a href="./cart.php" class="nav-link">
                    <span>
                        <i class="fa-solid fa-cart-shopping"></i>
                        Cart <?php echo $cartItems; ?> items - $<?php echo number_format($cartTotal, 2); ?>
                    </span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<header class="header">
    <div class="banner">
        <marquee class="banner-title">Welcome to GameMerch Hub</marquee>
    </div>
</header>

<main>

    <!-- Filter Buttons -->
    <div class="filter-buttons">
        <button class="filter-btn active" onclick="filterCategory('all')">All Products</button>
        <button class="filter-btn" onclick="filterCategory('ps5games')">PS5 Games</button>
        <button class="filter-btn" onclick="filterCategory('xboxgames')">Xbox Games</button>
        <button class="filter-btn" onclick="filterCategory('steamwallet')">Steam Wallet</button>
        <button class="filter-btn" onclick="filterCategory('esportsjersey')">Esports Jersey</button>
        <button class="filter-btn" onclick="filterCategory('collectibles')">Collectibles</button>
        <button class="filter-btn" onclick="filterCategory('customprint')">Custom Print</button>
    </div>

    <!-- Search Box -->
    <div class="search-box-container">
        <input type="text" class="search-box" id="searchInput"
               placeholder="Search products by name..."
               onkeyup="searchProducts()">
    </div>

    <!-- Product Listing -->
    <?php foreach ($categories as $catKey => $catProducts): ?>
        <div class="category-section" data-category="<?php echo htmlspecialchars($catKey, ENT_QUOTES, 'UTF-8'); ?>">
            <h2 class="category-title">
                <?php echo htmlspecialchars($catProducts[0]['category'], ENT_QUOTES, 'UTF-8'); ?>
            </h2>

            <div class="product-grid">
                <?php foreach ($catProducts as $product): ?>

                    <?php
                    $productID = (int)$product['id'];
                    $productName = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
                    $productPrice = number_format((float)$product['price'], 2);
                    $productImage = htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8');
                    ?>

                    <a href="./product.php?id=<?php echo $productID; ?>" class="product-box">
                        <div class="product-image">
                            <img src="<?php echo $productImage; ?>" alt="<?php echo $productName; ?>">
                        </div>
                        <div class="product-info">
                            <div class="product-name"><?php echo $productName; ?></div>
                            <div class="product-price">$<?php echo $productPrice; ?></div>
                        </div>
                    </a>

                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

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

    <p style="padding-left: 10px; padding-bottom: 10px;">
        © 2025 GameMerchHub.com. All Rights Reserved.
    </p>
</footer>

<script src="./home.js"></script>
<script src="./action.js"></script>

</body>
</html>
