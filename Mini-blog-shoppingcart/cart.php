<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Process remove from cart request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_cart'])) {
    $cart_id = $_POST['cart_id'];
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $_SESSION['user_id']]);
    header('Location: cart.php'); // Refresh the page to update the cart
    exit;
}

// Fetch cart items
$stmt = $pdo->prepare("SELECT cart.id AS cart_id, products.name, products.price, cart.quantity FROM cart INNER JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Body and background styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('images/background.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        header {
            text-align: center;
            padding: 50px 0;
            background-color: rgba(0, 0, 0, 0.7);
        }

        h1 {
            margin: 0;
            font-size: 3em;
            color: #fff;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .cart-list {
            list-style: none;
            padding: 0;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .cart-item span {
            font-size: 1.2em;
            color: #333;
        }

        .cart-item button {
            padding: 10px 20px;
            background-color: gray;
            color: white;
            border: none;
            font-size: 1em;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .cart-item button:hover {
            background-color:gray;
        }

        .cart-total {
            font-size: 1.5em;
            color: #fff;
            text-align: right;
            margin-top: 20px;
        }

        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .cart-actions a, .cart-actions button {
            padding: 12px 25px;
            background-color:gray;
            color: white;
            font-size: 1.2em;
            border-radius: 5px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .cart-actions a:hover, .cart-actions button:hover {
            background-color:gray;
        }

        footer {
            background-color: rgba(0, 0, 0, 0.7);
            text-align: center;
            padding: 20px;
        }

        footer p {
            margin: 0;
            color: #fff;
        }
    </style>
</head>
<body>
    <header>
        <h1>Your Cart</h1>
    </header>
    <div class="container">
        <?php if (count($cart_items) > 0): ?>
            <ul class="cart-list">
                <?php foreach ($cart_items as $item): ?>
                    <li class="cart-item">
                        <span><?= htmlspecialchars($item['name']) ?> (<?= $item['quantity'] ?> x $<?= number_format($item['price'], 2) ?>)</span>
                        <form method="post" style="display:inline-block;">
                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                            <button type="submit" name="remove_from_cart">Remove</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
            <h2 class="cart-total">Total: $<?= number_format($total, 2) ?></h2>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
      
    </div>
    <div class="container">
    <!-- Existing Cart Items and Total Display -->

    <!-- Back Button -->
    <div class="cart-actions">
        <a href="product.php">Back to Products</a>
    </div>
</div>

    <footer>
        <p>&copy; 2025 Your Store</p>
    </footer>
</body>
</html>
