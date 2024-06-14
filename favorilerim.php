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
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);
$username = $user['username'];

// Favori tarifleri (favorites) veritabanından çekin
$favorites_query = "
    SELECT recipes.id, recipes.title, recipes.description, recipes.image 
    FROM recipes 
    JOIN favorites ON recipes.id = favorites.recipe_id 
    WHERE favorites.user_id = ?";
$stmt = $conn->prepare($favorites_query);
$stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
$favorites_result = $stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Favori Tariflerim</title>
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
        .navbar .site-name a {
            color: #fff;
            text-decoration: none;
        }
        .navbar .user-info {
            display: flex;
            align-items: center;
        }
        .navbar .user-info span {
            margin-right: 15px;
        }
        .navbar .logout-button,
        .navbar .favorites-button {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
            margin-left: 10px;
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
            justify-content: space-between;
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
        .remove-favorite-button {
            background-color: #ffb3b3;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            color: #343a40;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="navbar">
        <div class="site-name"><a href="index.php">Tariflerim</a></div>
        <div class="user-info">
            <span><?php echo htmlspecialchars($username); ?></span>
            <a href="index.php" class="favorites-button">Tarifler</a>
            <a href="index.php?action=logout" class="logout-button">Çıkış Yap</a>
        </div>
    </div>
    <div class="container">
        <h1>Favori Tariflerim</h1>
        <?php while ($recipe = $favorites_result->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="recipe-card">
                <img src="<?php echo htmlspecialchars($recipe['image']); ?>" alt="Tarif Resmi">
                <div>
                    <h3><a href="tarif.php?id=<?php echo $recipe['id']; ?>" style="color: #50fa7b;"><?php echo htmlspecialchars($recipe['title']); ?></a></h3>
                    <p><?php echo htmlspecialchars($recipe['description']); ?></p>
                </div>
                <button class="remove-favorite-button" data-recipe-id="<?php echo $recipe['id']; ?>">Favorilerden Kaldır</button>
            </div>
        <?php endwhile; ?>
    </div>
    <script>
        $(document).ready(function() {
            $('.remove-favorite-button').on('click', function() {
                var recipeId = $(this).data('recipe-id');
                $.ajax({
                    url: 'remove_favorite.php',
                    type: 'POST',
                    data: { recipe_id: recipeId },
                    success: function(response) {
                        alert('Tarif favorilerden kaldırıldı!');
                        location.reload(); // Sayfayı yeniden yükleyerek güncelle
                    },
                    error: function() {
                        alert('Bir hata oluştu.');
                    }
                });
            });
        });
    </script>
</body>
</html>
