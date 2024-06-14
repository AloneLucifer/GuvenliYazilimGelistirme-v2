<?php
require_once 'auth.php';
require_once 'db.php';

// Oturum başlat
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Rol kontrolü
checkRole(['admin', 'editor']);

// Tarifleri veritabanından çekin
$recipes_query = "SELECT * FROM recipes";
$recipes_result = $conn->query($recipes_query);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tarif Listele</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.delete-button').on('click', function() {
                var recipeId = $(this).data('recipe-id');
                var row = $(this).closest('tr');
                $.ajax({
                    url: 'tarif_sil.php',
                    type: 'POST',
                    data: { id: recipeId },
                    success: function(response) {
                        alert('Tarif başarıyla silindi!');
                        row.remove();
                    },
                    error: function() {
                        alert('Bir hata oluştu.');
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2>Tarifler</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Başlık</th>
                    <th>Açıklama</th>
                    <th>Kategori</th>
                    <th>Resim</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($recipe = $recipes_result->fetchArray(SQLITE3_ASSOC)): ?>
                    <tr>
                        <td><?php echo $recipe['id']; ?></td>
                        <td><?php echo htmlspecialchars($recipe['title']); ?></td>
                        <td><?php echo htmlspecialchars($recipe['description']); ?></td>
                        <td><?php echo htmlspecialchars($recipe['category']); ?></td>
                        <td><img src="<?php echo htmlspecialchars($recipe['image']); ?>" alt="Tarif Resmi" style="max-width: 100px;"></td>
                        <td>
                            <button class="btn btn-danger delete-button" data-recipe-id="<?php echo $recipe['id']; ?>">Sil</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
