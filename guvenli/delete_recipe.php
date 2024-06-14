<?php
include 'auth.php';
include 'db.php';

// Oturum başlat
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Rol kontrolü
checkRole(['admin', 'editor']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipe_id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM recipes WHERE id = ?");
    $stmt->bindValue(1, $recipe_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $conn->lastErrorMsg();
    }

    $conn->close();
}
?>
