<?php
// Adatbázis kapcsolódás és lekérdezés külön függvényekben
require("kapcsolat.php");

/**
 * Adatok lekérése az adatbázisból.
 * @param mysqli $dbconn Az adatbázis kapcsolat.
 * @return array A lekérdezett adatok tömbje.
 */
function getContacts(mysqli $dbconn): array {
    $query = "SELECT * FROM nevjegyek";
    $result = mysqli_query($dbconn, $query);

    if (!$result) {
        // Hiba esetén üres tömb visszaadása
        return [];
    }

    $contacts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $contacts[] = $row;
    }

    return $contacts;
}

/**
 * JSON válasz küldése.
 * @param mixed $data A válasz tartalma.
 */
function sendJsonResponse($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

// Adatok lekérése és JSON válasz küldése
$contacts = getContacts($dbconn);
sendJsonResponse($contacts);
