<?php
require_once 'auth.php';
require_once 'db.php';

// Oturum başlatma işlemi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Rol kontrolü
checkRole(['admin', 'editor']);

// HTTP başlıklarında oturum ve rol bilgilerini göster
header("X-User-ID: " . $_SESSION['user_id']);
header("X-User-Role: " . $_SESSION['role']);

// Hata ayıklama modunu aktif et
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Yorum silme işlemi
if (isset($_POST['delete_comment'])) {
    $comment_id = intval($_POST['comment_id']);
    $delete_stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $delete_stmt->bindValue(1, $comment_id, SQLITE3_INTEGER);
    $delete_stmt->execute();
}

// Yorumları çek
$comments_query = "
SELECT c.id, c.comment, u.username, r.title 
FROM comments c 
JOIN users u ON c.user_id = u.id 
JOIN recipes r ON c.recipe_id = r.id 
ORDER BY c.created_at DESC";
$comments_result = $conn->query($comments_query);

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .comments-section {
            margin-top: 20px;
        }

        .comment {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .comment-header strong {
            font-size: 16px;
        }

        .comment-actions {
            display: flex;
            align-items: center;
        }

        .comment-actions form {
            margin: 0;
            padding: 0;
        }

        .comment-actions button {
            margin-left: 10px;
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

        .navbar .logout-button {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="admin.php">Admin Paneli</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="admin.php?action=tarif_ekle">Tarif Ekle</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin.php?action=tarif_listele">Tarifleri Listele</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin.php?action=yorum_sil">Yorum Sil</a>
                </li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="admin.php?action=rol_guncelle">Kullanıcı Rolü Güncelle</a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Ana Sayfaya Dön</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="cikis.php">Çıkış Yap</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <?php
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
            if ($action == 'tarif_ekle') {
                include 'tarif_ekle_formu.php';
            } elseif ($action == 'tarif_listele') {
                include 'tarif_listele.php';
            } elseif ($action == 'yorum_sil') {
                include 'yorum_sil.php';
            } elseif ($action == 'rol_guncelle' && $_SESSION['role'] == 'admin') {
                include 'rol_guncelle.php';
            }
        } else {
            echo "<h2 class='my-4'>Admin Paneline Hoşgeldiniz!</h2>";
        }
        ?>
    </div>
</body>
</html>
