<?php
session_start();
require 'includes/db.php'; // adjust path if needed

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $confirm_text = trim($_POST['confirm_text'] ?? '');

    if (!$current_password || !$new_password || !$confirm_password || !$confirm_text) {
        $errors[] = "All fields are required.";
    }

    if ($new_password !== $confirm_password) {
        $errors[] = "New password and confirmation do not match.";
    }

    if ($confirm_text !== "CONFIRM") {
        $errors[] = "You must type CONFIRM to proceed.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($current_password, $user['password_hash'])) {
            $errors[] = "Current password is incorrect.";
        } else {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $update->execute([$new_hash, $user_id]);
            $success = "✅ Password changed successfully!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Change Password - GameMerch Hub</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
<link rel="stylesheet" href="./style.css">

<style>
body { background:#111; font-family: Arial, Helvetica, sans-serif; color:#fff; }
.form-container { max-width:500px; margin:50px auto; }
.form-card { background:#1e1e2f; padding:25px; border-radius:12px; box-shadow:0 0 15px rgba(255,0,255,0.3); }
.form-card h2 { text-align:center; margin-bottom:20px; color:#ff00ff; font-weight:normal; }
.form-group { display:flex; flex-direction:column; margin-bottom:15px; }
.form-group label { margin-bottom:5px; color:#fff; font-weight:normal; }
.form-group input { padding:10px; border-radius:6px; border:1px solid #555; background:#2b2b3c; color:#fff; font-size:1rem; }
.form-group input::placeholder { color:#aaa; }
.btn { width:100%; padding:12px; background:#ff00ff; border:none; color:#fff; border-radius:6px; cursor:pointer; font-size:1rem; margin-top:10px; transition:0.3s; font-weight:normal; }
.btn:hover { background:#e600e6; }
.go-back-btn { width:100%; padding:12px; background:#555; border:none; color:#fff; border-radius:6px; cursor:pointer; font-size:1rem; margin-top:10px; transition:0.3s; font-weight:normal; }
.go-back-btn:hover { background:#777; }
.password-wrapper { position: relative; }
.password-wrapper input { width: 100%; padding-right: 35px; }
.password-wrapper i { position: absolute; right: 10px; top: 65%; transform: translateY(-50%); cursor: pointer; color: #aaa; }

/* Modal Styles */
#passwordModal {
    display:none;
    position:fixed;
    z-index:1000;
    left:0; top:0;
    width:100%; height:100%;
    background: rgba(0,0,0,0.6);
}
#passwordModal .modal-content {
    background:#2b2b3c;
    color:#fff;
    padding:25px;
    border-radius:10px;
    max-width:400px;
    margin:100px auto;
    text-align:center;
    position:relative;
}
#passwordModal .modal-content p {
    margin-top:20px;
    font-size:1rem;
}
#passwordModal .modal-content button {
    margin-top:15px;
    padding:8px 15px;
    background:#ff00ff;
    border:none;
    color:#fff;
    border-radius:6px;
    cursor:pointer;
}
#passwordModal .close {
    position:absolute;
    top:10px;
    right:15px;
    font-size:24px;
    cursor:pointer;
}
</style>
</head>
<body>

<div class="form-container">
    <div class="form-card">
        <h2><i class="fa-solid fa-lock"></i> Change Password</h2>

        <form method="POST">
            <div class="form-group password-wrapper">
                <label for="current_password">Current Password</label>
                <input type="password" name="current_password" id="current_password" placeholder="Type current password" required>
                <i class="fa-solid fa-eye" id="toggleCurrent"></i>
            </div>

            <div class="form-group password-wrapper">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" placeholder="Type new password" required>
                <i class="fa-solid fa-eye" id="toggleNew"></i>
            </div>

            <div class="form-group password-wrapper">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Retype new password" required>
                <i class="fa-solid fa-eye" id="toggleConfirm"></i>
            </div>

            <div class="form-group">
                <label for="confirm_text">Type CONFIRM to apply changes</label>
                <input type="text" name="confirm_text" id="confirm_text" placeholder="CONFIRM" required autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
            </div>

            <button type="submit" class="btn">Change Password</button>
        </form>

        <button onclick="window.location.href='profile.php'" class="go-back-btn">
            <i class="fa-solid fa-arrow-left"></i> Go Back
        </button>
    </div>
</div>

<!-- Password Change Modal -->
<div id="passwordModal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="modalMessage"></p>
        <button onclick="document.getElementById('passwordModal').style.display='none'">OK</button>
    </div>
</div>

<script>
// Password visibility toggles
function setupToggle(idInput, idIcon) {
    const input = document.getElementById(idInput);
    const icon = document.getElementById(idIcon);
    icon.addEventListener("click", () => {
        const showing = input.type === "text";
        input.type = showing ? "password" : "text";
        icon.classList.toggle("fa-eye", showing);
        icon.classList.toggle("fa-eye-slash", !showing);
    });
}

setupToggle("current_password", "toggleCurrent");
setupToggle("new_password", "toggleNew");
setupToggle("confirm_password", "toggleConfirm");

// Modal handling
const modal = document.getElementById('passwordModal');
const modalMsg = document.getElementById('modalMessage');
const closeBtn = document.querySelector('#passwordModal .close');

closeBtn.onclick = () => modal.style.display = 'none';
window.onclick = (e) => { if(e.target == modal) modal.style.display = 'none'; };

// Show modal with PHP messages
<?php if(!empty($errors)): ?>
modalMsg.innerHTML = "<?php foreach($errors as $err){ echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . '<br>'; } ?>";
modal.style.display = 'block';
<?php endif; ?>

<?php if($success): ?>
modalMsg.textContent = "<?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>";
modal.style.display = 'block';
<?php endif; ?>
</script>

</body>
</html>
