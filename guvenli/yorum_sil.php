<?php
require_once 'auth.php';
require_once 'db.php';

// Oturum başlatma işlemi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Rol kontrolü
checkRole(['admin', 'editor']);

// Yorumları çek
$comments_query = "SELECT c.id, c.comment, u.username, r.title FROM comments c JOIN users u ON c.user_id = u.id JOIN recipes r ON c.recipe_id = r.id ORDER BY c.created_at DESC";
$comments_result = $conn->query($comments_query);

// Yorum silme işlemi
if (isset($_POST['delete_comment'])) {
    $comment_id = intval($_POST['comment_id']);
    $delete_stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $delete_stmt->bindValue(1, $comment_id, SQLITE3_INTEGER);
    $delete_stmt->execute();
    $delete_stmt->close();
    header("Location: admin.php?action=yorum_sil");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yorumları Sil</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Yorumları Sil</h2>
        <div class="comments-section">
            <?php
            if ($comments_result->numColumns() > 0) {
                while ($comment = $comments_result->fetchArray(SQLITE3_ASSOC)) {
                    echo "<div class='comment'>";
                    echo "<div class='comment-header'>";
                    echo "<strong>" . htmlspecialchars($comment['username']) . "</strong> - <span>" . htmlspecialchars($comment['title']) . "</span>";
                    echo "</div>";
                    echo "<p>" . htmlspecialchars($comment['comment']) . "</p>";
                    echo "<div class='comment-actions'>";
                    echo "<form action='yorum_sil.php' method='post'>";
                    echo "<input type='hidden' name='comment_id' value='" . $comment['id'] . "'>";
                    echo "<button type='submit' name='delete_comment' class='btn btn-danger btn-sm'>Sil</button>";
                    echo "</form>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>Henüz yorum bulunmamaktadır.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
