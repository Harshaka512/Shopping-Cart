<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-image: url('images/background.jpg'); /* Replace with your image URL */
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: white;
        }

        h1 {
            margin-top: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }

        nav {
            margin-top: 20px;
            background-color: rgba(0, 0, 0, 0.7);
            width: 100%;
            padding: 10px 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            gap: 30px;
        }

        nav ul li {
            display: inline;
        }

        nav ul li a {
            text-decoration: none;
            color: gray;
            font-size: 18px;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        nav ul li a:hover {
            background-color: rgba(255, 255, 255, 0.9);
            color: black;
        }

        .content {
            margin-top: 20px;
            text-align: center;
        }

    </style>
</head>
<body>
    <h1>Welcome to the Dashboard</h1>

    <nav>
        <ul>
            <li><a href="index.php">Blog Posts</a></li>
            <li><a href="product.php">Products</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="content">
        <p>Explore the dashboard using the navigation links above.</p>
    </div>
</body>
</html>
