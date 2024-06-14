<?php
//session_start();
require_once 'db.php';

// Oturum başlatma işlemi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Rol kontrolü
checkRole(['admin']);

// Kullanıcı rolünü güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = intval($_POST['user_id']);
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bindValue(1, $role, SQLITE3_TEXT);
    $stmt->bindValue(2, $user_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo "Kullanıcı rolü başarıyla güncellendi.";
    } else {
        echo "Hata: " . $conn->lastErrorMsg();
    }

    $stmt->close();
}

// Mevcut kullanıcıları listele
$users_query = "SELECT id, username FROM users";
$users_result = $conn->query($users_query);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kullanıcı Rolü Güncelle</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Kullanıcı Rolü Güncelle</h2>
        <form action="admin.php?action=rol_guncelle" method="post">
            <div class="form-group">
                <label for="user_id">Kullanıcı Seç</label>
                <select class="form-control" id="user_id" name="user_id" required>
                    <?php while ($user = $users_result->fetchArray(SQLITE3_ASSOC)): ?>
                        <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="role">Rol Seç</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="editor">Editor</option>
                    <option value="subscriber">Subscriber</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Rol Güncelle</button>
        </form>
    </div>
</body>
</html>
