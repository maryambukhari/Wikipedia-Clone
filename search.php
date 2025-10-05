<?php
// search.php
session_start();
include 'db.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';

$where = "1=1"; // Base condition
$params = [];
if ($query) {
    $where .= " AND (title LIKE ? OR content LIKE ?)";
    $params[] = "%$query%";
    $params[] = "%$query%";
}
if ($category_id) {
    $where .= " AND category_id = ?";
    $params[] = $category_id;
}

$stmt = $pdo->prepare("SELECT a.*, c.name AS category FROM articles a JOIN categories c ON a.category_id = c.id WHERE $where AND a.status = 'approved'");
$stmt->execute($params);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - Wikipedia Clone</title>
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
        .search-bar {
            margin-bottom: 20px;
        }
        .search-bar input, .search-bar select {
            padding: 10px;
            margin: 5px;
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
        .article-card {
            margin: 10px 0;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        a {
            color: #1a73e8;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            color: #d81b60;
        }
        .no-articles {
            color: #666;
            text-align: center;
        }
        @media (max-width: 768px) {
            .container { padding: 10px; }
            .search-bar input, .search-bar select { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search Articles</h2>
        <div class="search-bar">
            <input type="text" id="search" value="<?php echo htmlspecialchars($query); ?>" placeholder="Search articles...">
            <select id="category_id">
                <option value="">All Categories</option>
                <?php
                $stmt = $pdo->query("SELECT * FROM categories");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $selected = ($row['id'] == $category_id) ? 'selected' : '';
                    echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                }
                ?>
            </select>
            <button onclick="search()">Search</button>
        </div>
        <?php if ($articles): ?>
            <?php foreach ($articles as $article): ?>
                <div class="article-card">
                    <a href="#" onclick="redirect('article_view.php?id=<?php echo $article['id']; ?>')"><?php echo htmlspecialchars($article['title']); ?></a>
                    <p>Category: <?php echo htmlspecialchars($article['category']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-articles">No articles found.</p>
        <?php endif; ?>
        <a href="#" onclick="redirect('index.php')">Back to Home</a>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
        function search() {
            let query = document.getElementById('search').value;
            let category_id = document.getElementById('category_id').value;
            let url = 'search.php?';
            if (query) url += 'q=' + encodeURIComponent(query);
            if (category_id) url += (query ? '&' : '') + 'category_id=' + category_id;
            redirect(url);
        }
    </script>
</body>
</html>
