<?php
require_once 'db_setup.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new SQLite3('yemektarifi.db');

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Tüm alanlar gereklidir.";
    } elseif ($password !== $confirm_password) {
        $error = "Şifreler eşleşmiyor.";
    } else {
        try {
            // Store the password in plain text
            $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
            $stmt->bindValue(1, $username, SQLITE3_TEXT);
            $stmt->bindValue(2, $password, SQLITE3_TEXT);
            $stmt->execute();
            
            $_SESSION['success'] = "Kayıt başarılı, şimdi giriş yapabilirsiniz.";
            header("Location: giris_kayit.php");
            exit();
        } catch (Exception $e) {
            $error = "Bir hata oluştu: " . $e->getMessage();
        }
    }

    $_SESSION['error'] = $error;
    header("Location: giris_kayit.php");
    exit();
}
?>

