<?php
function checkRole($roles) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles)) {
        header("Location: index.php");
        exit();
    }
}
?>
