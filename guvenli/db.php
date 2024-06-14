<?php
require_once 'db_setup.php';

$dbFile = 'yemektarifi.db';

try {
    $conn = new SQLite3($dbFile);
} catch (Exception $e) {
    echo "Veritabanına bağlanılamadı: " . $e->getMessage();
    exit();
}
?>
