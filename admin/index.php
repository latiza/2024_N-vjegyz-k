<?php
session_start();

// Ellenőrizze, hogy az admin be van-e jelentkezve
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.html"); // Ha nincs bejelentkezve, irányítsa át a bejelentkezési oldalra
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
    <h1>Üdvözlünk az Admin Felületen!</h1>
    <p>Itt tudsz navigálni az adminisztrációs funkciók között.</p>
    <ul>
        <li><a href="dashboard.php">Admin Dashboard</a></li>
        <li><a href="some-other-admin-page.php">Egyéb Admin Funkció</a></li>
        <li><a href="logout.php">Kilépés</a></li>
    </ul>
</body>
</html>
