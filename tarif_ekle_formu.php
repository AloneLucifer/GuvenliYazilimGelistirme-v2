<?php
//session_start();
require_once 'db.php';

// Kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: giris_kayit.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $is_private = isset($_POST['is_private']) ? 1 : 0;
    $user_id = $_SESSION['user_id'];

    $image_path = '';
    if (!empty($_FILES['image']['name'])) {
        $uploads_dir = 'uploads';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0755, true);
        }
        $image_path = $uploads_dir . '/' . basename($_FILES['image']['name']);
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            echo "Resim yükleme hatası!";
            exit();
        }
    }

    $stmt = $conn->prepare("INSERT INTO recipes (title, description, category, is_private, image, created_by) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bindValue(1, $title, SQLITE3_TEXT);
    $stmt->bindValue(2, $description, SQLITE3_TEXT);
    $stmt->bindValue(3, $category, SQLITE3_TEXT);
    $stmt->bindValue(4, $is_private, SQLITE3_INTEGER);
    $stmt->bindValue(5, $image_path, SQLITE3_TEXT);
    $stmt->bindValue(6, $user_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo "Yeni tarif başarıyla eklendi.";
    } else {
        echo "Hata: " . $conn->lastErrorMsg();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tarif Ekle</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Tarif Ekle</h2>
        <form action="admin.php?action=tarif_ekle" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Başlık</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Açıklama</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="category">Kategori</label>
                <input type="text" class="form-control" id="category" name="category" required>
            </div>
            <div class="form-group">
                <label for="image">Resim</label>
                <input type="file" class="form-control-file" id="image" name="image">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="is_private" name="is_private">
                <label class="form-check-label" for="is_private">Özel</label>
            </div>
            <button type="submit" class="btn btn-primary">Tarif Ekle</button>
        </form>
    </div>
</body>
</html>
