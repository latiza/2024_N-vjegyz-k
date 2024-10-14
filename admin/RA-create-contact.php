<?php
session_start();
if (!isset($_SESSION['belepett'])) { 
	header("Location: false.html"); // azaz ide
	exit();
}

require "../kapcsolat.php"; // Kapcsolat az adatbázissal

// Ellenőrizzük, hogy POST kérés érkezett-e
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Adatok lekérése és tisztítása
    $nev = mysqli_real_escape_string($dbconn, strip_tags(trim($_POST['nev'])));
    $cegnev = mysqli_real_escape_string($dbconn, strip_tags(trim($_POST['cegnev'])));
    $mobil = mysqli_real_escape_string($dbconn, strip_tags(trim($_POST['mobil'])));
    $email = mysqli_real_escape_string($dbconn, strip_tags(trim($_POST['email'])));

    // Hibaüzenetek tárolása
    $hibak = [];

    // Adatok érvényességének ellenőrzése
    if (empty($nev)) {
        $hibak[] = "A név mező nem lehet üres!";
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $hibak[] = "Az e-mail cím formátuma érvénytelen!";
    }
    if (!empty($mobil) && !preg_match('/^[0-9]{9,}$/', $mobil)) {
        $hibak[] = "A mobil szám formátuma nem megfelelő!";
    }

    // Kép feltöltése, ha van fájl
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        // MIME típusok ellenőrzése
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $mimeType = mime_content_type($_FILES['foto']['tmp_name']);
        if (!in_array($mimeType, $allowedMimeTypes)) {
            $hibak[] = "Nem megfelelő képformátum!";
        }

        // Fájl feltöltés és név generálás
        if (empty($hibak)) {
            $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $fotoFileName = time() . '.' . $extension; // Egyedi fájlnév időbélyeggel

            move_uploaded_file($_FILES['foto']['tmp_name'], "../kepek/{$fotoFileName}");
        }
    } else {
        // Ha nincs feltöltött kép, használjunk alapértelmezett képet
        $fotoFileName = 'nincskep.png';
    }

    // Ha nincs hiba, akkor az adatokat tároljuk az adatbázisban
    if (empty($hibak)) {
        $sql = "INSERT INTO nevjegyek (nev, cegnev, mobil, email, foto) VALUES (?, ?, ?, ?, ?)";
        $stmt = $dbconn->prepare($sql);
        $stmt->bind_param('sssss', $nev, $cegnev, $mobil, $email, $fotoFileName);

        if ($stmt->execute()) {
            // Sikeres beszúrás esetén sikerüzenet visszaküldése
            http_response_code(201); // 201 Created státusz
            echo json_encode(['message' => 'Névjegy sikeresen létrehozva!'], JSON_UNESCAPED_UNICODE);
        } else {
            // Hibakezelés, ha az SQL végrehajtás sikertelen
            http_response_code(500); // 500 Internal Server Error
            echo json_encode(['error' => 'Hiba történt az adatbázis művelet során!'], JSON_UNESCAPED_UNICODE);
        }
    } else {
        // Ha hibák vannak, azokat JSON formában visszaküldjük
        http_response_code(400); // 400 Bad Request
        echo json_encode(['errors' => $hibak], JSON_UNESCAPED_UNICODE);
    }
}
