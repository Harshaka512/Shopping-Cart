<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle search
$search_term = '';
if (isset($_POST['search'])) {
    $search_term = $_POST['search'];
}

// Fetch products with search filter if any
$query = "SELECT * FROM products";
if ($search_term) {
    $query .= " WHERE name LIKE ?";
}

$stmt = $pdo->prepare($query);
if ($search_term) {
    $stmt->execute(["%" . $search_term . "%"]);
} else {
    $stmt->execute();
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $product_id = $_POST['product_id'];
        $quantity = 1; // Default quantity is 1

        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + 1");
        $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
        header('Location: cart.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
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
            background-color: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .search-form {
            margin-bottom: 50px;
            text-align: center;
        }

        .search-form input {
            padding: 10px;
            width: 50%;
            font-size: 1em;
            border-radius: 5px;
            border: none;
        }

        .search-form button {
            padding: 5px 10px;
            font-size: 1em;
            border: none;
            background-color: gray;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: gray;
        }

        .product-card {
            background-color: rgba(255, 255, 255, 0.7);
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .product-images {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .product-images img {
            border-radius: 8px;
            width: 200px;
            height: 200px;
            object-fit: cover;
        }

        button {
            padding: 10px 20px;
            background-color: gray;
            color: white;
            font-size: 1.2em;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            margin-top: 10px;
            display: block;
            width: 100%;
        }

        button:hover {
            background-color: gray;
        }

        footer {
            background-color: rgba(0, 0, 0, 0.7);
            text-align: center;
            padding: 20px;
            bottom: 0;
            width: 100%;
        }

        footer p {
            margin: 0;
            color: #fff;
        }

        a {
            color: white;
            text-decoration: none;
            font-size: 1.2em;
        }

        a:hover {
            color: white;
        }

        .back-button {
            padding: 10px 20px;
            background-color: gray;
            color: white;
            font-size: 1.2em;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            text-align: center;
            display: inline-block;
            margin-top: 20px;
        }

        .back-button:hover {
            background-color:gray;
        }
    </style>
</head>
<body>
    <header>
        <h1>Product Listing</h1>
    </header>
    <div class="container">
        <!-- Search Bar -->
        <form method="post" class="search-form">
            <input type="text" name="search" placeholder="Search for a product..." value="<?= htmlspecialchars($search_term) ?>">
            <button type="submit">Search</button>
        </form>
        
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <h2><?= htmlspecialchars($product['name']) ?></h2>
                <p>Price: $<?= number_format($product['price'], 2) ?></p>

                <!-- Display product images -->
                <div class="product-images">
                    <img src="images/product1.jpg" width="200" height="200"> 
                    <img src="images/product2.jpg" width="200" height="200"> 
                    <img src="images/product3.jpg" width="200" height="200"> 
                </div>

                <form method="post">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" name="add_to_cart">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
        <hr>
        <a href="cart.php"class="back-button">View Cart</a>
        <a href="dashboard.php" class="back-button">Back to Dashboard</a> <!-- Added back button -->
    </div>
    <footer>
        <p>&copy; 2025 Your Store</p>
    </footer>
</body>
</html>
