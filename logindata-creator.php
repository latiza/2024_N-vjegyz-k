<?php
require "kapcsolat.php";
// A jelszó hash-elése és tárolása az adatbázisban
$jelszo = 'rita'; // A felhasználó jelszava
$hash = password_hash($jelszo, PASSWORD_DEFAULT); // A jelszó hash-elése

// Az SQL lekérdezés az új felhasználó mentéséhez
$sql = "INSERT INTO felhasznalok (email, jelszo) VALUES (?, ?)";
$stmt = $dbconn->prepare($sql);
$email = 'rita@gmail.com'; // Az admin email címe
$stmt->bind_param('ss', $email, $hash);

// Végrehajtjuk a lekérdezést és visszajelzést adunk
if ($stmt->execute()) {
    echo "A felhasználó sikeresen mentésre került az adatbázisba!";
} else {
    echo "Hiba történt a felhasználó mentésekor: " . $stmt->error;
}

// Kapcsolat bezárása
$stmt->close();
$dbconn->close();

