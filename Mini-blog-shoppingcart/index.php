<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch posts
$posts = $pdo->query("SELECT posts.*, users.name FROM posts INNER JOIN users ON posts.user_id = users.id ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

$edit_post = null; // To store the post being edited

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $title = htmlspecialchars($_POST['title']);
        $content = htmlspecialchars($_POST['content']);
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $title, $content]);
        header('Location: index.php');
        exit;
    } elseif (isset($_POST['edit_mode'])) {
        // Fetch the post data to be edited
        $post_id = $_POST['post_id'];
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
        $stmt->execute([$post_id, $_SESSION['user_id']]);
        $edit_post = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif (isset($_POST['edit'])) {
        // Update the post
        $post_id = $_POST['post_id'];
        $title = htmlspecialchars($_POST['title']);
        $content = htmlspecialchars($_POST['content']);
        $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $content, $post_id, $_SESSION['user_id']]);
        header('Location: index.php');
        exit;
    } elseif (isset($_POST['delete'])) {
        // Delete the post
        $post_id = $_POST['post_id'];
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
        $stmt->execute([$post_id, $_SESSION['user_id']]);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Blog</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-image: url('images/background.jpg'); /* Replace with your image URL */
            background-size: cover;
            background-position: center;
            color: white;
        }

        h1, h2 {
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }

        form {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            margin: 20px auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        .post {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 600px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .post h2 {
            margin: 0 0 10px;
        }

        .post p {
            margin: 5px 0;
        }

        .post form {
            display: inline-block;
        }

        .back-button {
            display: block;
            text-align: center;
            margin: 20px auto;
        }

        .back-button a {
            text-decoration: none;
            color: white;
            background-color: #333;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .back-button a:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

<h1>Blog Posts</h1>
<?php foreach ($posts as $post): ?>
    <div class="post">
        <h2><?= $post['title'] ?></h2>
        <p>By: <?= $post['name'] ?> | <?= $post['created_at'] ?></p>
        <p><?= $post['content'] ?></p>
        <?php if ($post['user_id'] === $_SESSION['user_id']): ?>
            <form method="post">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <button type="submit" name="edit_mode">Edit</button>
            </form>
            <form method="post">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <button type="submit" name="delete">Delete</button>
            </form>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<h2><?= $edit_post ? "Edit Post" : "Create New Post" ?></h2>
<form method="post">
    <?php if ($edit_post): ?>
        <input type="hidden" name="post_id" value="<?= $edit_post['id'] ?>">
    <?php endif; ?>
    <label>Title:</label>
    <input type="text" name="title" value="<?= htmlspecialchars($edit_post['title'] ?? '') ?>" required><br>
    <label>Content:</label>
    <textarea name="content" required><?= htmlspecialchars($edit_post['content'] ?? '') ?></textarea><br>
    <button type="submit" name="<?= $edit_post ? 'edit' : 'create' ?>">
        <?= $edit_post ? 'Update' : 'Create' ?>
    </button>
    
</form>
<div class="back-button">
    <a href="dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>
