<?php
session_start();
require_once "includes/db.php";

// --- Admin Access Check ---
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Initialize messages
$alert_message = "";

// HANDLE FORM SUBMISSIONS

// Update product stock/price
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $stock = $_POST['stock_quantity'] ?? 0;
    $price = $_POST['price'] ?? 0;

    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = ?, price = ? WHERE id = ?");
    $stmt->execute([$stock, $price, $product_id]);
    $alert_message = "Product ID $product_id updated successfully!";
}

// Delete product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$product_id]);
    $alert_message = "Product ID $product_id deleted successfully!";
}

// Update order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $total = $_POST['total_amount'] ?? 0;
    $discount = $_POST['discount_amount'] ?? 0;
    $payment_method = $_POST['payment_method'] ?? '';
    $payment_status = $_POST['payment_status'] ?? '';
    $delivery_status = $_POST['delivery_status'] ?? '';
    $redeem_key = $_POST['redeem_key'] ?? '';
    $delivery_address = $_POST['delivery_address'] ?? '';

    $stmt = $pdo->prepare("
        UPDATE orders 
        SET total_amount=?, discount_amount=?, payment_method=?, payment_status=?, delivery_status=?, redeem_key=?, delivery_address=? 
        WHERE id=?
    ");
    $stmt->execute([$total, $discount, $payment_method, $payment_status, $delivery_status, $redeem_key, $delivery_address, $order_id]);
    $alert_message = "Order ID $order_id updated successfully!";
}

// Delete order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];
    $pdo->prepare("DELETE FROM order_items WHERE order_id=?")->execute([$order_id]);
    $pdo->prepare("DELETE FROM orders WHERE id=?")->execute([$order_id]);
    $alert_message = "Order ID $order_id deleted successfully!";
}

// Update ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_ticket'])) {
    $ticket_id = $_POST['ticket_id'];
    $status = $_POST['status'] ?? '';
    $pdo->prepare("UPDATE tickets SET status=? WHERE id=?")->execute([$status, $ticket_id]);
    $alert_message = "Ticket ID $ticket_id updated successfully!";
}

// FETCH DATA FOR DISPLAY
$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
$orders = $pdo->query("
    SELECT o.id AS order_id, u.username, o.total_amount, o.discount_amount, o.payment_method, 
           o.payment_status, o.delivery_status, o.delivery_address, o.redeem_key, o.created_at
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
$tickets = $pdo->query("
    SELECT t.id AS ticket_id, u.username, t.subject, t.message, t.status, t.created_at
    FROM tickets t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel - GameMerch Hub</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
<link href="https://fonts.cdnfonts.com/css/frizen-land" rel="stylesheet">
<link rel="stylesheet" href="./style.css">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body { background:#111; color:#fff; font-family: Arial, sans-serif; }
nav { background:#1a1a1a; padding:10px; border-bottom:2px solid #ff00ff; }
nav ul { display:flex; justify-content:flex-start; gap:20px; list-style:none; margin:0; padding:0; }
nav a { color:#fff; text-decoration:none; font-weight:bold; }
nav a:hover { color:#ff00ff; }
main { margin:20px auto; width:95%; max-width:1400px; }
.card { background:#1e1e2f; border-radius:10px; padding:15px; margin-bottom:25px; overflow-x:auto; }
h2 { color:#ff00ff; margin-bottom:15px; }
table { width:100%; border-collapse:collapse; table-layout:fixed; }
th, td { padding:10px; text-align:left; border-bottom:1px solid #555; vertical-align:middle; }
th { background:#ff00ff; color:#fff; }
input, select { padding:5px; border-radius:4px; border:none; width:100%; box-sizing:border-box; }
input.stock-input, input.redeem-input, input.order-input { width:100px; }
.btn { padding:4px 8px; margin:2px 0; border:none; border-radius:4px; cursor:pointer; font-size:0.85rem; display:block; width:100%; }
.btn-edit { background:#4CAF50; color:#fff; }
.btn-delete { background:#f44336; color:#fff; }
img.product-img { width:50px; height:auto; border-radius:5px; }
form.inline { display:inline-block; margin:0; padding:0; width:100%; }
.alert { background:#00ff88; color:#000; padding:10px; margin-bottom:10px; border-radius:5px; text-align:center; font-weight:bold; }
</style>
</head>
<body>

<?php if($alert_message): ?>
<div class="alert"><?= htmlspecialchars($alert_message); ?></div>

<script>
Swal.fire({
    icon: 'success',
    title: 'Success',
    text: "<?= htmlspecialchars($alert_message); ?>",
    confirmButtonColor: '#ff00ff',
    background: '#1e1e2f',
    color: '#fff'
});
</script>
<?php endif; ?>

<nav>
<ul>
    <li><a href="#stock">Stock Management <i class="fa-solid fa-boxes"></i></a></li>
    <li><a href="#orders">Orders & Payments <i class="fa-solid fa-receipt"></i></a></li>
    <li><a href="#tickets">Tickets/Issues <i class="fa-solid fa-ticket"></i></a></li>
    <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
</ul>
</nav>

<main>

<!-- STOCK MANAGEMENT -->
<section id="stock" class="card">
<h2>Stock Management</h2>
<table>
<thead>
<tr>
<th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Action</th>
</tr>
</thead>
<tbody>
<?php foreach($products as $p): ?>
<tr>
<form method="POST" class="inline">
<td><img class="product-img" src="<?= htmlspecialchars($p['image_path']); ?>" alt="<?= htmlspecialchars($p['name']); ?>"></td>
<td><?= htmlspecialchars($p['name']); ?></td>
<td><?= htmlspecialchars($p['category']); ?></td>
<td><input type="number" class="order-input" name="price" value="<?= $p['price']; ?>" step="0.01"></td>
<td><input type="number" class="stock-input" name="stock_quantity" value="<?= $p['stock_quantity']; ?>"></td>
<td>
<input type="hidden" name="product_id" value="<?= $p['id']; ?>">
<button type="submit" name="update_product" class="btn btn-edit">Update</button>
<button type="submit" name="delete_product" class="btn btn-delete">Delete</button>
</td>
</form>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</section>

<!-- ORDERS MANAGEMENT -->
<section id="orders" class="card">
<h2>Orders Management</h2>
<table>
<thead>
<tr>
<th>User</th><th>Total</th><th>Discount</th><th>Payment Method</th><th>Payment Status</th>
<th>Delivery Status</th><th>Redeem Key</th><th>Delivery Address</th><th>Action</th>
</tr>
</thead>
<tbody>
<?php foreach($orders as $o): ?>
<tr>
<form method="POST" class="inline">
<td><?= htmlspecialchars($o['username']); ?></td>
<td><input type="number" name="total_amount" class="order-input" value="<?= $o['total_amount']; ?>" step="0.01"></td>
<td><input type="number" name="discount_amount" class="order-input" value="<?= $o['discount_amount']; ?>" step="0.01"></td>
<td><input type="text" name="payment_method" class="order-input" value="<?= htmlspecialchars($o['payment_method']); ?>"></td>
<td>
<select name="payment_status">
<option value="pending" <?= $o['payment_status']=='pending'?'selected':''; ?>>Pending</option>
<option value="completed" <?= $o['payment_status']=='completed'?'selected':''; ?>>Completed</option>
<option value="failed" <?= $o['payment_status']=='failed'?'selected':''; ?>>Failed</option>
</select>
</td>
<td>
<select name="delivery_status">
<option value="pending" <?= $o['delivery_status']=='pending'?'selected':''; ?>>Pending</option>
<option value="shipped" <?= $o['delivery_status']=='shipped'?'selected':''; ?>>Shipped</option>
<option value="delivered" <?= $o['delivery_status']=='delivered'?'selected':''; ?>>Delivered</option>
<option value="cancelled" <?= $o['delivery_status']=='cancelled'?'selected':''; ?>>Cancelled</option>
</select>
</td>
<td><input type="text" name="redeem_key" class="redeem-input" value="<?= htmlspecialchars($o['redeem_key']); ?>"></td>
<td><input type="text" name="delivery_address" class="order-input" value="<?= htmlspecialchars($o['delivery_address']); ?>"></td>
<td>
<input type="hidden" name="order_id" value="<?= $o['order_id']; ?>">
<button type="submit" name="update_order" class="btn btn-edit">Update</button>
<button type="submit" name="delete_order" class="btn btn-delete">Delete</button>
</td>
</form>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</section>

<!-- TICKETS MANAGEMENT -->
<section id="tickets" class="card">
<h2>Tickets / Issues</h2>
<table>
<thead>
<tr><th>User</th><th>Subject</th><th>Message</th><th>Status</th><th>Action</th></tr>
</thead>
<tbody>
<?php foreach($tickets as $t): ?>
<tr>
<form method="POST" class="inline">
<td><?= htmlspecialchars($t['username']); ?></td>
<td><?= htmlspecialchars($t['subject']); ?></td>
<td><?= htmlspecialchars($t['message']); ?></td>
<td>
<select name="status">
<option value="open" <?= $t['status']=='open'?'selected':''; ?>>Open</option>
<option value="resolved" <?= $t['status']=='resolved'?'selected':''; ?>>Resolved</option>
</select>
</td>
<td>
<input type="hidden" name="ticket_id" value="<?= $t['ticket_id']; ?>">
<button type="submit" name="update_ticket" class="btn btn-edit">Update</button>
</td>
</form>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</section>

</main>
<footer class="card">
<p>© 2025 GameMerchHub.com. All Rights Reserved.</p>
</footer>

</body>
</html>
