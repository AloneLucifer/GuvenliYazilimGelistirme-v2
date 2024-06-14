<?php
require_once 'db.php';
session_start();

// Tarif ID'sini al
$recipe_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($recipe_id > 0) {
    $stmt = $conn->prepare("SELECT title, description, category, image, is_private, created_by FROM recipes WHERE id = ?");
    $stmt->bindValue(1, $recipe_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $recipe = $result->fetchArray(SQLITE3_ASSOC);
    if (!$recipe) {
        echo "Geçersiz tarif ID'si.";
        exit();
    }
    $stmt->close();
} else {
    echo "Geçersiz tarif ID'si.";
    exit();
}

// Yorumları çek
$comments_stmt = $conn->prepare("SELECT c.comment, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.recipe_id = ? ORDER BY c.created_at DESC");
$comments_stmt->bindValue(1, $recipe_id, SQLITE3_INTEGER);
$comments_result = $comments_stmt->execute();

$comments = [];
while ($row = $comments_result->fetchArray(SQLITE3_ASSOC)) {
    $comments[] = $row;
}

// Yorum ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'] ?? 0;

    if ($user_id > 0 && !empty($comment)) {
        $insert_comment_stmt = $conn->prepare("INSERT INTO comments (user_id, recipe_id, comment) VALUES (?, ?, ?)");
        $insert_comment_stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
        $insert_comment_stmt->bindValue(2, $recipe_id, SQLITE3_INTEGER);
        $insert_comment_stmt->bindValue(3, $comment, SQLITE3_TEXT);
        $insert_comment_stmt->execute();
        $insert_comment_stmt->close();
        header("Location: tarif.php?id=" . $recipe_id);
        exit();
    } else {
        echo "Yorum eklenemedi. Lütfen giriş yapın ve yorum alanını boş bırakmayın.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($recipe['title']); ?></title>
    <link rel="stylesheet" href="./styletarif.css">
</head>
<body>
    <div class="navbar">
        <div class="site-name"><a href="index.php">Tariflerim</a></div>
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
            <a href="index.php?action=logout" class="logout-button">Çıkış Yap</a>
        </div>
    </div>
    <div class="container">
        <div class="recipe-detail">
            <h1><?php echo htmlspecialchars($recipe['title']); ?></h1>
            <img src="<?php echo htmlspecialchars($recipe['image']); ?>" alt="Tarif Resmi">
            <p><strong>Kategori:</strong> <?php echo htmlspecialchars($recipe['category']); ?></p>
            <p><strong>Açıklama:</strong> <?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>
        </div>

        <div class="comments-section">
            <h2>Yorumlar</h2>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="tarif.php?id=<?php echo $recipe_id; ?>" method="post">
                    <div class="form-group">
                        <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Yorumunuzu yazın..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Yorumu Gönder</button>
                </form>
            <?php else: ?>
                <p>Yorum yapmak için <a href="giris_kayit.php">giriş yapın</a>.</p>
            <?php endif; ?>
        </div>

        <div class="comments-list">
            <h2>Yapılmış Yorumlar</h2>
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <p><strong><?php echo htmlspecialchars($comment['username']); ?>:</strong> <?php echo htmlspecialchars($comment['comment']); ?></p> <!-- XSS Fixed -->
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Henüz yorum yapılmamış.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

