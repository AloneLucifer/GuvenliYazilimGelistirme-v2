<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipe_id = intval($_POST['recipe_id']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(2, $recipe_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $conn->lastErrorMsg();
    }

    $conn->close();
}
?>
