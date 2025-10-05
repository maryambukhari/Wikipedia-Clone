<?php
// article_create.php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>redirect('login.php');</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $user_id = $_SESSION['user_id'];
    $status = ($_SESSION['role'] == 'admin') ? 'approved' : 'pending';

    try {
        $stmt = $pdo->prepare("INSERT INTO articles (title, content, user_id, category_id, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $content, $user_id, $category_id, $status]);
        $article_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO revisions (article_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$article_id, $user_id, $content]);
        echo "<script>alert('Article created successfully!'); redirect('index.php');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Article - Wikipedia Clone</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        h2 {
            color: #1a73e8;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        textarea {
            height: 200px;
        }
        button {
            padding: 10px 20px;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #d81b60;
        }
        a {
            color: #1a73e8;
            text-decoration: none;
            display: block;
            margin-top: 10px;
        }
        a:hover {
            color: #d81b60;
        }
        @media (max-width: 768px) {
            .container { padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create Article</h2>
        <form method="POST">
            <input type="text" name="title" placeholder="Article Title" required>
            <textarea name="content" placeholder="Article Content" required></textarea>
            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php
                $stmt = $pdo->query("SELECT * FROM categories");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
            <button type="submit">Create Article</button>
            <a href="#" onclick="redirect('index.php')">Back to Home</a>
        </form>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
