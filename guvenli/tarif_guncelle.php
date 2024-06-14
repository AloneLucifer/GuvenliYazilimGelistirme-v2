<?php
session_start();
require_once 'db.php';

// Kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: giris_kayit.php");
    exit();
}

// Tarif güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipe_id = intval($_POST['recipe_id']);
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $is_private = isset($_POST['is_private']) ? 1 : 0;

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
    } else {
        // Mevcut resmi koruma
        $stmt = $conn->prepare("SELECT image FROM recipes WHERE id = ?");
        $stmt->bindValue(1, $recipe_id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $recipe = $result->fetchArray(SQLITE3_ASSOC);
        $image_path = $recipe['image'];
    }

    $stmt = $conn->prepare("UPDATE recipes SET title = ?, description = ?, category = ?, is_private = ?, image = ? WHERE id = ?");
    $stmt->bindValue(1, $title, SQLITE3_TEXT);
    $stmt->bindValue(2, $description, SQLITE3_TEXT);
    $stmt->bindValue(3, $category, SQLITE3_TEXT);
    $stmt->bindValue(4, $is_private, SQLITE3_INTEGER);
    $stmt->bindValue(5, $image_path, SQLITE3_TEXT);
    $stmt->bindValue(6, $recipe_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo "Tarif başarıyla güncellendi.";
    } else {
        echo "Hata: " . $conn->lastErrorMsg();
    }

    $conn->close();
    exit();
}

// Mevcut tarifleri listele
$recipes_query = "SELECT id, title FROM recipes";
$recipes_result = $conn->query($recipes_query);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tarif Güncelle</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#recipe_id').on('change', function() {
                var recipeId = $(this).val();
                if (recipeId) {
                    $.ajax({
                        url: 'get_recipe_details.php',
                        type: 'GET',
                        data: { id: recipeId },
                        success: function(response) {
                            var data = JSON.parse(response);
                            $('#title').val(data.title);
                            $('#description').val(data.description);
                            $('#category').val(data.category);
                            $('#is_private').prop('checked', data.is_private == 1);
                            $('#current_image').attr('src', data.image);
                        }
                    });
                }
            });
        });
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2>Tarif Güncelle</h2>
        <form action="tarif_guncelle.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="recipe_id">Tarif Seç</label>
                <select class="form-control" id="recipe_id" name="recipe_id" required>
                    <option value="">Tarif Seçiniz</option>
                    <?php while ($recipe = $recipes_result->fetchArray(SQLITE3_ASSOC)): ?>
                        <option value="<?php echo $recipe['id']; ?>"><?php echo htmlspecialchars($recipe['title']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
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
                <img id="current_image" src="" alt="Mevcut Resim" style="max-width: 200px; margin-top: 10px;">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="is_private" name="is_private">
                <label class="form-check-label" for="is_private">Özel</label>
            </div>
            <button type="submit" class="btn btn-primary">Tarif Güncelle</button>
        </form>
    </div>
</body>
</html>
