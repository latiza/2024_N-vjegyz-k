<?php
require "kapcsolat.php"; // Csatlakozás az adatbázishoz

// Az SQL lekérdezés a 'felhasznalok' tábla létrehozásához
$sql = "CREATE TABLE IF NOT EXISTS felhasznalok (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    jelszo VARCHAR(255) NOT NULL
)";

// A lekérdezés végrehajtása és visszajelzés adása
if (mysqli_query($dbconn, $sql)) {
    echo "A 'felhasznalok' tábla sikeresen létrejött!";
} else {
    echo "Hiba történt a tábla létrehozásakor: " . mysqli_error($dbconn);
}

// Kapcsolat bezárása
mysqli_close($dbconn);

