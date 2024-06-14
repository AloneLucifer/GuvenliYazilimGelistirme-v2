<?php
session_start();
require_once 'db.php';

// Kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: giris_kayit.php");
    exit();
}

// Kullanıcı bilgilerini alın
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);
$username = $user['username'];
$role = $user['role'];

// Çıkış işlemi
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: giris_kayit.php");
    exit();
}

// Tarifleri (recipes) veritabanından çekin
if ($role == 'admin' || $role == 'editor') {
    $recipes_query = "SELECT id, title, description, image FROM recipes";
} else {
    $recipes_query = "SELECT id, title, description, image FROM recipes WHERE is_private = 0";
}
$recipes_result = $conn->query($recipes_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ana Sayfa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles.css">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #282a36;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            color: #fff;
        }
        .navbar {
            background-color: #343a40;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .navbar .site-name {
            font-size: 24px;
            font-weight: bold;
        }
        .navbar .search-form {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
        }
        .navbar .search-form input[type="text"] {
            padding: 5px;
            border-radius: 5px;
            border: none;
            margin-right: 10px;
        }
        .navbar .search-form button {
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        .navbar .user-info {
            display: flex;
            align-items: center;
        }
        .navbar .user-info span {
            margin-right: 15px;
        }
        .navbar .logout-button {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
        }
        .container {
            padding: 20px;
            width: 100%;
            max-width: 1000px;
            margin-top: 70px; /* Navbar ile içerik arasında boşluk bırak */
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .recipe-card {
            background-color: #44475a;
            border: 1px solid #6272a4;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 15px;
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            color: #fff;
            display: flex;
            align-items: center;
        }
        .recipe-card img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 15px;
        }
        .recipe-card h3 {
            margin-top: 0;
            color: #50fa7b;
        }
        .recipe-card p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="site-name"><a href="index.php">Tariflerim</a></div>
        <form class="search-form" action="search.php" method="get">
            <input type="text" name="query" placeholder="Tarif Ara">
            <button type="submit">Ara</button>
        </form>
        <div class="user-info">
            <span><?php echo htmlspecialchars($username); ?></span>
            <a href="index.php?action=logout" class="logout-button">Çıkış Yap</a>
        </div>
    </div>
    <div class="container">
        <h1>Tarifler</h1>
        <?php while ($recipe = $recipes_result->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="recipe-card">
                <img src="<?php echo htmlspecialchars($recipe['image']); ?>" alt="Tarif Resmi">
                <div>
                    <h3><a href="tarif.php?id=<?php echo $recipe['id']; ?>" style="color: #50fa7b;"><?php echo htmlspecialchars($recipe['title']); ?></a></h3>
                    <p><?php echo htmlspecialchars($recipe['description']); ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
