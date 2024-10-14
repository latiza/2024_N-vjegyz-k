<?php
session_start();

// Ellenőrizze, hogy a felhasználó be van-e jelentkezve admin státusszal
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin Felület</title>
</head>
<body>
    <h1>Üdvözlünk az admin felületen!</h1>
    <p><a href="logout.php">Kilépés</a></p>
</body>
</html>
