<?php
session_start();

// Check login status for disabling Register/Login links
$isLoggedIn = isset($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMerch Hub - ESRB Ratings</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link href="https://fonts.cdnfonts.com/css/frizen-land" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">

    <style>
        html, body {
            margin: 0;
            height: 100%;
            background-color: #12121e;
        }

        main.flex-center {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: calc(100vh - 120px);
            padding: 10px 20px;
        }

        .esrb-container {
            width: 100%;
            max-width: 1200px;
            background-color: #1e1e2f;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
            color: #fff;
            display: flex;
            flex-direction: column;
            gap: 20px;
            flex-grow: 1;
        }

        .esrb-container h2 {
            text-align: center;
            color: #ff00ff;
            margin-bottom: 30px;
            font-size: 2rem;
        }

        .ratings-scroll {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding-bottom: 10px;
            scroll-behavior: smooth;
        }

        .ratings-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .ratings-scroll::-webkit-scrollbar-thumb {
            background-color: #ff00ff;
            border-radius: 4px;
        }

        .rating-card {
            min-width: 250px;
            flex: 0 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #000;
            color: #fff;
            padding: 20px;
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .rating-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 0, 255, 0.3);
        }

        .rating-icon {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }

        .rating-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .rating-info h3 {
            margin: 0;
            font-size: 1.4rem;
            text-align: center;
        }

        .rating-info p {
            margin: 5px 0 0;
            font-size: 1rem;
            line-height: 1.4;
            text-align: center;
        }

        @media (max-width: 768px) {
            .rating-card {
                min-width: 200px;
            }
        }

        /* Disabled link styling */
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
                    <span>
                        <?= $isLoggedIn ? htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') : "Guest"; ?>
                    </span>
                </a>
            </li>
        </ul>

        <i class="fa-solid fa-bars fa-2x" id="menu-icon"></i>

        <div id="menu" class="hidden">
            <ul class="nav-lower flex-space-around">

                <!-- Register (greyed out when logged in) -->
                <li class="nav-list">
                    <a href="./register.php"
                       class="nav-link <?= $isLoggedIn ? 'disabled-link' : ''; ?>">
                       Register
                    </a>
                </li>

                <li class="nav-list">
                    <a href="./contact.php" class="nav-link">Contact</a>
                </li>

                <!-- Login (greyed out when logged in) -->
                <li class="nav-list">
                    <a href="./login.php"
                       class="nav-link <?= $isLoggedIn ? 'disabled-link' : ''; ?>">
                       Login
                    </a>
                </li>

                <!-- Logout disabled if not logged in -->
                <li class="nav-list">
                    <a href="./logout.php" class="nav-link <?= !$isLoggedIn ? 'disabled-link' : ''; ?>">Logout</a>
                </li>

                <li class="nav-list">
                    <a href="./cart.php" class="nav-link">
                        <span>
                        <i class="fa-solid fa-cart-shopping"></i>
                        Cart 
                        <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?> items
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <main class="flex-center">
        <div class="main-content esrb-container">

            <h2>ESRB Game Ratings</h2>

            <div class="ratings-scroll">

                <div class="rating-card">
                    <div class="rating-icon">
                        <img src="https://www.esrb.org/wp-content/uploads/2019/05/E.svg" alt="EC Rating">
                    </div>
                    <div class="rating-info">
                        <h3>Early Childhood (EC)</h3>
                        <p>Games suitable for young children, typically ages 3 and older. No inappropriate content.</p>
                    </div>
                </div>

                <div class="rating-card">
                    <div class="rating-icon">
                        <img src="https://www.esrb.org/wp-content/uploads/2019/05/E10plus.svg" alt="E Rating">
                    </div>
                    <div class="rating-info">
                        <h3>Everyone (E)</h3>
                        <p>Games suitable for all ages. May contain minimal cartoon or fantasy violence and mild language.</p>
                    </div>
                </div>

                <div class="rating-card">
                    <div class="rating-icon">
                        <img src="https://www.esrb.org/wp-content/uploads/2019/05/T.svg" alt="T Rating">
                    </div>
                    <div class="rating-info">
                        <h3>Teen (T)</h3>
                        <p>Games suitable for ages 13+. May include violence, suggestive themes, mild language, or minimal blood.</p>
                    </div>
                </div>

                <div class="rating-card">
                    <div class="rating-icon">
                        <img src="https://www.esrb.org/wp-content/uploads/2019/05/M.svg" alt="M Rating">
                    </div>
                    <div class="rating-info">
                        <h3>Mature (M)</h3>
                        <p>Games suitable for ages 17+. May contain intense violence, blood and gore, sexual content, and strong language.</p>
                    </div>
                </div>

                <div class="rating-card">
                    <div class="rating-icon">
                        <img src="https://www.esrb.org/wp-content/uploads/2019/05/AO.svg" alt="AO Rating">
                    </div>
                    <div class="rating-info">
                        <h3>Adults Only (AO)</h3>
                        <p>Games suitable for adults only (18+). Contains prolonged intense violence, sexual content, and/or gambling with real currency.</p>
                    </div>
                </div>

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

            <ul style="padding-top: 10px;">
                <h3 style="color: rgb(255, 0, 255);">Follow Us</h3>
                <li><a href="#" style="color: #1877F2; font-size: 2rem;"><i class="fa-brands fa-square-facebook"></i></a></li>
                <li><a href="#" style="color: white; font-size: 2rem;"><i class="fa-brands fa-square-x-twitter"></i></a></li>
                <li><a href="#" style="font-size: 2rem;"><i class="fa-brands fa-square-instagram"
                        style="background: linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);-webkit-background-clip:text;-webkit-text-fill-color:transparent;"></i></a>
                </li>
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

    <script>
        const menuIcon = document.getElementById('menu-icon');
        const menu = document.getElementById('menu');
        menuIcon.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>

</body>
</html>
