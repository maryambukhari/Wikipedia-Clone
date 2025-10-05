<?php
// profile.php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>redirect('login.php');</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

    // Check for duplicate username or email (excluding current user)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $stmt->execute([$username, $email, $user_id]);
    if ($stmt->fetch()) {
        echo "<script>alert('Username or email already taken.');</script>";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$username, $email, $password, $user_id]);
            echo "<script>alert('Profile updated successfully!'); redirect('profile.php');</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Wikipedia Clone</title>
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
        h2, h3 {
            color: #1a73e8;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
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
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        a:hover {
            color: #d81b60;
        }
        .info {
            margin-bottom: 20px;
        }
        .error {
            color: #d81b60;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .container { padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Profile</h2>
        <div class="info">
            <p><strong>Current Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Current Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
        </div>
        <h3>Edit Profile</h3>
        <form method="POST">
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <input type="password" name="password" placeholder="New Password (leave blank to keep current)">
            <button type="submit">Update Profile</button>
        </form>
        <a href="#" onclick="redirect('index.php')">Back to Home</a>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
