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
        $allowed_mime_types = ['image/jpeg', 'image/png'];
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $max_file_size = 2 * 1024 * 1024; // 2 MB

        $file_tmp_path = $_FILES['image']['tmp_name'];
        $file_name = basename($_FILES['image']['name']);
        $file_size = $_FILES['image']['size'];
        $file_type = mime_content_type($file_tmp_path);
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Check MIME type
        if (!in_array($file_type, $allowed_mime_types)) {
            echo "Geçersiz dosya türü!";
            exit();
        }

        // Check file extension
        if (!in_array($file_extension, $allowed_extensions)) {
            echo "Geçersiz dosya uzantısı!";
            exit();
        }

        // Check file size
        if ($file_size > $max_file_size) {
            echo "Dosya boyutu 2 MB'yi aşamaz!";
            exit();
        }

        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0755, true);
        }
        $image_path = $uploads_dir . '/' . uniqid() . '.' . $file_extension;
        if (!move_uploaded_file($file_tmp_path, $image_path)) {
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
        <form action="tarif_ekle.php" method="post" enctype="multipart/form-data">
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
