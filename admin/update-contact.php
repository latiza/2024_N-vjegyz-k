<?php
// Kapcsolódás az adatbázishoz
require "../kapcsolat.php"; 

// Ellenőrizzük, hogy PUT vagy PATCH kérés érkezett-e
if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'PATCH') {
    // A PUT/PATCH adatok lekérése
    parse_str(file_get_contents("php://input"), $_PUT);
    
    // Tisztítsuk meg a bemeneteket
    $id = (int) $_PUT['id'];
    $nev = mysqli_real_escape_string($dbconn, strip_tags(ucwords(strtolower(trim($_PUT['nev'])))));
    $cegnev = mysqli_real_escape_string($dbconn, strip_tags(trim($_PUT['cegnev'])));
    $mobil = mysqli_real_escape_string($dbconn, strip_tags(trim($_PUT['mobil'])));
    $email = mysqli_real_escape_string($dbconn, strip_tags(strtolower(trim($_PUT['email']))));

    // Változók vizsgálata
    $hibak = [];
    if (empty($nev)) {
        $hibak[] = "Nem adtál meg nevet!";
    } elseif (strlen($nev) < 5) {
        $hibak[] = "Rossz nevet adtál meg!";
    }

    if (!empty($mobil) && strlen($mobil) < 9) {
        $hibak[] = "Rossz mobil számot adtál meg!";
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $hibak[] = "Rossz e-mail címet adtál meg!";
    }

    // Hibaüzenetek kezelése
    if (!empty($hibak)) {
        // JSON válasz hiba esetén
        http_response_code(400);
        echo json_encode(['errors' => $hibak], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    // Adatbázis frissítése
    $sql = "UPDATE nevjegyek SET nev = '{$nev}', cegnev = '{$cegnev}', mobil = '{$mobil}', email = '{$email}' WHERE id = {$id}";
    if (mysqli_query($dbconn, $sql)) {
        // Sikeres válasz
        http_response_code(200);
        echo json_encode(['message' => 'Az adatok sikeresen frissültek!'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        // Hiba az SQL végrehajtása során
        http_response_code(500);
        echo json_encode(['error' => 'Hiba történt az adatok frissítésekor!'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} else {
    // Hibás HTTP-módszer
    http_response_code(405);
    echo json_encode(['error' => 'Hibás HTTP metódus!'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
