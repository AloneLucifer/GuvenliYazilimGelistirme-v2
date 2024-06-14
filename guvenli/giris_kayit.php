<?php
require_once 'db_setup.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new SQLite3('yemektarifi.db');

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = "Kullanıcı adı ve şifre gereklidir.";
    } else {
        try {
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
            $stmt->bindValue(1, $username, SQLITE3_TEXT);
            $stmt->bindValue(2, $password, SQLITE3_TEXT);
            $result = $stmt->execute();
            $user = $result->fetchArray(SQLITE3_ASSOC);

            if ($user) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['username'] = $user['username'];

                if ($user['role'] == 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = "Geçersiz kullanıcı adı veya şifre!";
            }
        } catch (Exception $e) {
            $error = "Bir hata oluştu: " . $e->getMessage();
        }
    }

    $_SESSION['error'] = $error;
    header("Location: giris_kayit.php");
    exit();
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login & Signup Form</title>
    <link rel="stylesheet" href="./stylegiris.css">
</head>
<body>
<div class="wrapper">
  <div class="title-text">
    <div class="title login">Login Form</div>
    <div class="title signup">Signup Form</div>
  </div>
  <div class="form-container">
    <div class="slide-controls">
      <input type="radio" name="slide" id="login" checked>
      <input type="radio" name="slide" id="signup">
      <label for="login" class="slide login">Login</label>
      <label for="signup" class="slide signup">Signup</label>
      <div class="slider-tab"></div>
    </div>
    <div class="form-inner">
      <!-- Giriş Formu -->
      <form action="giris_kayit.php" method="post" class="login">
        <div class="field">
          <input type="text" name="username" placeholder="Kullanıcı Adı" required>
        </div>
        <div class="field">
          <input type="password" name="password" placeholder="Şifre" required>
        </div>
        <div class="pass-link"><a href="#">Şifremi unuttum?</a></div>
        <div class="field btn">
          <div class="btn-layer"></div>
          <input type="submit" value="Giriş Yap">
        </div>
        <div class="signup-link">Üye değil misiniz? <a href="#">Şimdi kayıt olun</a></div>
      </form>
      <!-- Kayıt Formu -->
      <form action="kayit.php" method="post" class="signup">
        <div class="field">
          <input type="text" name="username" placeholder="Kullanıcı Adı" required>
        </div>
        <div class="field">
          <input type="password" name="password" placeholder="Şifre" required>
        </div>
        <div class="field">
          <input type="password" name="confirm_password" placeholder="Şifreyi Onayla" required>
        </div>
        <div class="field btn">
          <div class="btn-layer"></div>
          <input type="submit" value="Kayıt Ol">
        </div>
      </form>
    </div>
  </div>
</div>
<script>
const loginText = document.querySelector(".title-text .login");
const loginForm = document.querySelector("form.login");
const loginBtn = document.querySelector("label.login");
const signupBtn = document.querySelector("label.signup");
const signupLink = document.querySelector("form .signup-link a");
signupBtn.onclick = () => {
  loginForm.style.marginLeft = "-50%";
  loginText.style.marginLeft = "-50%";
};
loginBtn.onclick = () => {
  loginForm.style.marginLeft = "0%";
  loginText.style.marginLeft = "0%";
};
signupLink.onclick = () => {
  signupBtn.click();
  return false;
};
</script>
</body>
</html>
