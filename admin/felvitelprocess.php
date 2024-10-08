<?php
header('Content-Type: application/json; charset=UTF-8');


// Ellenőrizzük, hogy POST kérés érkezett-e
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Kapcsolódás az adatbázishoz
    require("../kapcsolat.php");

    // Változók tisztítása és ellenőrzése
    $nev    = strip_tags(ucwords(strtolower(trim($_POST['nev']))));
    $cegnev = strip_tags(trim($_POST['cegnev']));
    $mobil  = strip_tags(trim($_POST['mobil']));
    $email  = strip_tags(strtolower(trim($_POST['email'])));

    // Hibák gyűjtése
    $hibak = [];

    if (empty($nev) || strlen($nev) < 5) {
        $hibak[] = "A névnek legalább 5 karakter hosszúnak kell lennie.";
    }

    if (!empty($mobil) && strlen($mobil) < 9) {
        $hibak[] = "A mobil szám túl rövid.";
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $hibak[] = "Érvénytelen email formátum.";
    }

    if ($_FILES['foto']['error'] == 0 && $_FILES['foto']['size'] > 2000000) {
        $hibak[] = "A feltöltött fájl mérete túl nagy.";
    }

    // Fájlkezelés
    $mime = array("image/jpeg", "image/pjpeg", "image/gif", "image/png");
    if ($_FILES['foto']['error'] == 0 && !in_array($_FILES['foto']['type'], $mime)) {
        $hibak[] = "Nem megfelelő képformátum.";
    }

    // Ha nincs hiba, feldolgozzuk az adatokat
    if (empty($hibak)) {
        // Fájlnév létrehozása
        $kiterjesztes = match ($_FILES['foto']['type']) {
            "image/png" => ".png",
            "image/gif" => ".gif",
            default => ".jpg",
        };

        $foto = uniqid() . $kiterjesztes;

        // Adatok beszúrása az adatbázisba
        $sql = "INSERT INTO nevjegyek (foto, nev, cegnev, mobil, email) VALUES ('$foto', '$nev', '$cegnev', '$mobil', '$email')";

        if (mysqli_query($dbconn, $sql)) {
            // Kép mozgatása a megfelelő helyre
            move_uploaded_file($_FILES['foto']['tmp_name'], "../kepek/{$foto}");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Hiba történt az adatbázisba írás során.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => implode("\n", $hibak)]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Érvénytelen kérés.']);
}
