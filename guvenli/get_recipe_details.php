<?php
include 'auth.php';
include 'db.php';

// Oturum başlat
if (session_status() == PHP_SESSION_NONE) {
    session_start();

// Rol kontrolü
checkRole(['admin', 'editor']);
}
if (isset($_GET['id'])) {
    $recipe_id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ?");
    $stmt->bindValue(1, $recipe_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $recipe = $result->fetchArray(SQLITE3_ASSOC);

    echo json_encode($recipe);

    $conn->close();
}
?>
