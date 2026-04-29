<?php
session_start();
require_once "includes/db.php"; 

// User info
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id  = intval($_SESSION['user_id']);
$username = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');
$isLoggedIn = isset($_SESSION['username']);

// Profile update 
$update_success = "";
$update_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = htmlspecialchars(trim($_POST['full_name']), ENT_QUOTES, 'UTF-8');
    $email     = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone     = htmlspecialchars(trim($_POST['phone']), ENT_QUOTES, 'UTF-8');
    $address   = htmlspecialchars(trim($_POST['address']), ENT_QUOTES, 'UTF-8');

    if (empty($full_name) || empty($email)) {
        $update_error = "Full name and email cannot be empty.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $update_error = "Invalid email format.";
    } else {
        try {
            $check = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
            $check->bindValue(':email', $email);
            $check->bindValue(':id', $user_id, PDO::PARAM_INT);
            $check->execute();

            if ($check->rowCount() > 0) {
                $update_error = "That email is already registered to another account.";
            } else {
                $update = $pdo->prepare("
                    UPDATE users 
                    SET full_name = :full_name, email = :email, phone = :phone, address = :address
                    WHERE id = :id
                ");
                $update->bindValue(':full_name', $full_name);
                $update->bindValue(':email', $email);
                $update->bindValue(':phone', $phone);
                $update->bindValue(':address', $address);
                $update->bindValue(':id', $user_id, PDO::PARAM_INT);
                $update->execute();

                $update_success = "Profile updated successfully!";
            }
        } catch (PDOException $e) {
            $update_error = "Database Error: " . $e->getMessage();
        }
    }
}

//  Fetch updated profile 
$userQuery = $pdo->prepare("
    SELECT username, full_name, email, phone, address, member_since
    FROM users WHERE id = :id
");
$userQuery->bindValue(':id', $user_id, PDO::PARAM_INT);
$userQuery->execute();
$profile = $userQuery->fetch(PDO::FETCH_ASSOC);

// XSS protection
foreach ($profile as $k => $v) {
    $profile[$k] = htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

//  Order history 
$orderQuery = $pdo->prepare("
    SELECT 
        oi.product_name, 
        oi.price, 
        oi.quantity,
        o.redeem_key, 
        o.delivery_status, 
        o.created_at
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC, oi.id ASC
");
$orderQuery->execute([$user_id]);
$order_history = $orderQuery->fetchAll(PDO::FETCH_ASSOC);

/* XSS Protection */
foreach ($order_history as $i => $row) {
    foreach ($row as $k => $v) {
        $order_history[$i][$k] = htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
    }
}

//  Support tickets 
$ticketQuery = $pdo->prepare("
    SELECT id, subject, status, created_at
    FROM tickets
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$ticketQuery->execute([$user_id]);
$tickets = $ticketQuery->fetchAll(PDO::FETCH_ASSOC);

/* XSS Protection */
foreach ($tickets as $i => $row) {
    foreach ($row as $k => $v) {
        $tickets[$i][$k] = htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
    }
}

// Cart info 
$cart_total_items = 0;
$cart_total_price = 0.00;

if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $price = (float)$item['price'];
        $qty   = (int)$item['quantity'];

        $cart_total_price += $price * $qty;
        $cart_total_items += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GameMerch Hub - Profile</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
<link href="https://fonts.cdnfonts.com/css/frizen-land" rel="stylesheet">
<link rel="stylesheet" href="./style.css">

<style>
/* INPUTS */
input[readonly] {
    background-color: #2b2b3c;
    color: #fff;
    border: none;
    padding: 6px 10px;
    width: 70%;
}
.info-row { margin-bottom: 10px; display:flex; justify-content: space-between; align-items:center; }
.info-label { font-weight: bold; }

/* TABLES */
.ticket-table, .order-table { width: 100%; border-collapse: collapse; margin-top:10px; }
.ticket-table th, .ticket-table td, .order-table th, .order-table td { border: 1px solid #555; padding: 8px; text-align:left; }
.ticket-status-open { color: #ff0000; font-weight:bold; }
.ticket-status-resolved { color: #00ff88; font-weight:bold; }
.delivery-pending { color: #ffbb00; font-weight:bold; }
.delivery-shipped { color: #00bbff; font-weight:bold; }
.delivery-delivered { color: #00ff88; font-weight:bold; }
.delivery-cancelled { color: #ff0000; font-weight:bold; }

/* BUTTONS */
.btn { padding: 8px 15px; border:none; border-radius:6px; cursor:pointer; background-color:#ff00ff; color:#fff; margin-top:10px; text-decoration: none; display:inline-block; }
.btn:hover { background-color:#e600e6; }
.disabled-link { pointer-events: none; color: grey !important; opacity: 0.6; }

/* MODAL */
#updateModal {
    display: none; 
    position: fixed;
    z-index: 1000;
    padding-top: 100px;
    left: 0; top: 0;
    width: 100%; height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}
#updateModal .modal-content {
    background-color: #2b2b3c;
    color: #fff;
    margin: auto;
    padding: 20px;
    border-radius: 10px;
    width: 80%;
    max-width: 400px;
    text-align: center;
}
#updateModal .close {
    color: #fff;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}
</style>
</head>
<body>

<!-- NAVIGATION -->
<nav>
    <ul class="nav-upper flex-space-around">
        <li class="nav-list">
            <a href="./home.php" class="nav-link">
                <img src="./image/Logo/Logo.png" alt="logolink">
            </a>
        </li>
        <li class="nav-list">
            <a href="./profile.php" class="nav-link">
                <span><?= $username ?></span>
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
            <li class="nav-list"><a href="./logout.php" class="nav-link <?= !$isLoggedIn ? 'disabled-link' : '' ?>">Logout</a></li>
            <li class="nav-list">
                <a href="./cart.php" class="nav-link">
                    <span><i class="fa-solid fa-cart-shopping"></i> 
                    Cart <?= $cart_total_items ?> items - $<?= number_format($cart_total_price, 2) ?>
                    </span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<main>
<div class="profile-container">

<!-- PROFILE INFO -->
<section class="profile-info card">
    <h2 class="section-heading"><i class="fa-solid fa-user"></i> My Profile</h2>

    <form id="profile-form" method="POST" action="">
        <div class="profile-data">
            <?php foreach($profile as $label => $value): ?>
            <div class="info-row">
                <span class="info-label"><?= ucfirst(str_replace('_',' ',$label)); ?>:</span>
                <input type="text" name="<?= $label ?>" value="<?= $value ?>" readonly>
            </div>
            <?php endforeach; ?>

            <button type="button" class="btn" id="edit-profile-btn">Edit Profile</button>
            <button type="submit" class="btn" id="save-profile-btn" style="display:none;">Save Changes</button>
        </div>
        <div style="text-align:center; margin-top:10px;">
            <a href="password_change.php" class="btn">Change Password</a>
        </div>
    </form>
</section>

<!-- ORDER HISTORY -->
<section class="order-history card">
    <h2 class="section-heading"><i class="fa-solid fa-shopping-bag"></i> Order History</h2>
    <table class="order-table">
        <thead>
            <tr>
                <th>SN</th>
                <th>Product Name</th>
                <th>Price (Each)</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Key/Code</th>
                <th>Delivery Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php $sn=1; foreach($order_history as $order): ?>
            <tr>
                <td><?= $sn++; ?></td>
                <td><?= $order['product_name']; ?></td>
                <td>$<?= number_format($order['price'],2); ?></td>
                <td><?= $order['quantity']; ?></td>
                <td>$<?= number_format($order['price'] * $order['quantity'], 2); ?></td>
                <td><?= $order['redeem_key']; ?></td>
                <td class="delivery-<?= strtolower($order['delivery_status']); ?>">
                    <?= ucfirst($order['delivery_status']); ?>
                </td>
                <td><?= $order['created_at']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<!-- SUPPORT TICKETS -->
<section class="ticket-section card">
    <h2 class="section-heading"><i class="fa-solid fa-ticket"></i> My Tickets</h2>
    <table class="ticket-table">
        <thead>
            <tr>
                <th>SN</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php $sn=1; foreach($tickets as $ticket): ?>
            <tr>
                <td><?= $sn++; ?></td>
                <td><?= $ticket['subject']; ?></td>
                <td class="<?= strtolower($ticket['status'])==='open'?'ticket-status-open':'ticket-status-resolved'; ?>">
                    <?= ucfirst($ticket['status']); ?>
                </td>
                <td><?= $ticket['created_at']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

</div>
</main>

<!-- UPDATE MODAL -->
<div id="updateModal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="modal-message"></p>
    </div>
</div>

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
            <li><a href="#" style="font-size: 2rem;"><i class="fa-brands fa-square-instagram" style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);-webkit-background-clip: text;-webkit-text-fill-color: transparent;"></i></a></li>
            <li><a href="#" style="color: #E60023; font-size: 2rem;"><i class="fa-brands fa-square-pinterest"></i></a></li>
            <li><a href="#" style="color: #FF0000; font-size: 2rem;"><i class="fa-brands fa-square-youtube"></i></a></li>
        </ul>
        <ul>
            <h3 style="color: rgb(255, 0, 255);">Help</h3>
            <li><a class="foot-link" href="./contact.php">Contact Us</a></li>
            <li><a class="foot-link" href="./Terms_and_conditions.php">Terms and Conditions</a></li>
        </ul>
    </div>
</footer>

<script>
/* MENU TOGGLE */
const menuIcon = document.getElementById('menu-icon');
const menu = document.getElementById('menu');
menuIcon.addEventListener('click', () => menu.classList.toggle('hidden'));

/* PROFILE EDIT TOGGLE */
const editBtn = document.getElementById('edit-profile-btn');
const saveBtn = document.getElementById('save-profile-btn');
const profileInputs = document.querySelectorAll('#profile-form input');

editBtn.addEventListener('click', () => {
    profileInputs.forEach(input => input.removeAttribute('readonly'));
    editBtn.style.display = 'none';
    saveBtn.style.display = 'inline-block';
});

/* MODAL DIALOG */
const modal = document.getElementById("updateModal");
const modalMessage = document.getElementById("modal-message");
const closeModal = document.querySelector("#updateModal .close");
closeModal.onclick = () => modal.style.display = "none";
window.onclick = (e) => { if(e.target == modal) modal.style.display = "none"; };

// Show modal on PHP messages
<?php if($update_success): ?>
modalMessage.textContent = "<?= $update_success ?>";
modal.style.display = "block";
<?php endif; ?>
<?php if($update_error): ?>
modalMessage.textContent = "<?= $update_error ?>";
modal.style.display = "block";
<?php endif; ?>
</script>

</body>
</html>
