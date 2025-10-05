<?php
// index.php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wikipedia Clone</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #1a73e8;
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            animation: slideIn 1s ease-in-out;
        }
        @keyframes slideIn {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }
        .nav {
            display: flex;
            justify-content: space-between;
            background: #fff;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .nav a {
            color: #1a73e8;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
            margin: 0 10px;
        }
        .nav a:hover {
            color: #d81b60;
        }
        .featured, .recent {
            margin: 20px 0;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .featured:hover, .recent:hover {
            transform: scale(1.02);
        }
        .article-card {
            margin: 10px 0;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .article-card a {
            color: #1a73e8;
            text-decoration: none;
            cursor: pointer;
        }
        .article-card a:hover {
            color: #d81b60;
        }
        .article-card span {
            color: #666;
            font-size: 14px;
        }
        .search-bar {
            margin: 20px 0;
            text-align: center;
        }
        .search-bar input {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .search-bar button {
            padding: 10px 20px;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .search-bar button:hover {
            background: #d81b60;
        }
        .no-articles {
            color: #666;
            text-align: center;
            font-style: italic;
        }
        .error {
            color: #d81b60;
            text-align: center;
        }
        @media (max-width: 768px) {
            .container { padding: 10px; }
            .search-bar input { width: 100%; }
            .nav { flex-direction: column; align-items: center; }
            .nav a { margin: 5px 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Wikipedia Clone</h1>
    </div>
    <div class="nav">
        <div>
            <a href="#" onclick="redirect('index.php')">Home</a>
            <a href="#" onclick="redirect('search.php')">Search</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="#" onclick="redirect('article_create.php')">Create Article</a>
                <a href="#" onclick="redirect('profile.php')">Profile</a>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="#" onclick="redirect('moderate.php')">Moderate</a>
                <?php endif; ?>
                <a href="#" onclick="redirect('logout.php')">Logout</a>
            <?php else: ?>
                <a href="#" onclick="redirect('signup.php')">Signup</a>
                <a href="#" onclick="redirect('login.php')">Login</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="container">
        <div class="search-bar">
            <input type="text" id="search" placeholder="Search articles...">
            <button onclick="search()">Search</button>
        </div>
        <div class="featured">
            <h2>Featured Articles</h2>
            <?php
            try {
                $stmt = $pdo->query("SELECT a.*, c.name AS category FROM articles a JOIN categories c ON a.category_id = c.id WHERE a.status = 'approved' ORDER BY RAND() LIMIT 3");
                $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($articles) {
                    foreach ($articles as $row) {
                        echo "<div class='article-card'><a href='#' onclick=\"redirect('article_view.php?id=" . $row['id'] . "')\">" . htmlspecialchars($row['title']) . "</a> <span>(" . htmlspecialchars($row['category']) . ")</span></div>";
                    }
                } else {
                    echo "<p class='no-articles'>No featured articles available. Create or approve some articles!</p>";
                }
            } catch (PDOException $e) {
                echo "<p class='error'>Error fetching articles: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
        <div class="recent">
            <h2>Recently Updated</h2>
            <?php
            try {
                $stmt = $pdo->query("SELECT a.*, c.name AS category FROM articles a JOIN categories c ON a.category_id = c.id WHERE a.status = 'approved' ORDER BY a.updated_at DESC LIMIT 3");
                $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($articles) {
                    foreach ($articles as $row) {
                        echo "<div class='article-card'><a href='#' onclick=\"redirect('article_view.php?id=" . $row['id'] . "')\">" . htmlspecialchars($row['title']) . "</a> <span>(" . htmlspecialchars($row['category']) . ")</span></div>";
                    }
                } else {
                    echo "<p class='no-articles'>No recently updated articles available. Create or approve some articles!</p>";
                }
            } catch (PDOException $e) {
                echo "<p class='error'>Error fetching articles: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
        function search() {
            let query = document.getElementById('search').value;
            redirect('search.php?q=' + encodeURIComponent(query));
        }
    </script>
</body>
</html>
