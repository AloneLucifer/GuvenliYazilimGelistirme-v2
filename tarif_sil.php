<?php
include 'auth.php';
include 'db.php';

checkRole(['admin', 'editor']);

// Tarif ID'sini al ve doğrula
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM recipes WHERE id = ?");
    $stmt->bindValue(1, $id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo "Tarif başarıyla silindi.";
    } else {
        echo "Hata: " . $conn->lastErrorMsg();
    }

    $stmt->close();
} else {
    echo "Geçersiz tarif ID'si.";
}

$conn->close();

header("Location: admin.php?action=tarif_listele");
exit();
?>
